import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, HTML_ENDPOINT, TEST_PAGE_HANDLES, TestField, THEMES, waitForLokiComponents} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Field Components button group (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('renders the options as a radio group', async ({page}) => {
            const field = new TestField(page, 'button_group');
            const group = field.root().getByRole('radiogroup', {name: 'Test button group'});

            await expect(group).toBeVisible();
            await expect(group.getByRole('radio')).toHaveCount(4);
            await expect(group.getByRole('radio', {name: 'Red'})).toHaveAttribute('aria-checked', 'true');
            await expect(group.getByRole('radio', {name: 'Red'})).toHaveAttribute('tabindex', '0');
            await expect(group.getByRole('radio', {name: 'Green'})).toHaveAttribute('aria-checked', 'false');
            await expect(group.getByRole('radio', {name: 'Green'})).toHaveAttribute('tabindex', '-1');
            await expect(group.getByRole('radio', {name: 'Black'})).toBeDisabled();
            expect(await field.getComponentData('value')).toBe('red');
        });

        test('saves a clicked option', async ({page}) => {
            const field = new TestField(page, 'button_group');
            const group = field.root().getByRole('radiogroup');

            const requestPromise = field.waitForUpdate();
            await group.getByRole('radio', {name: 'Blue'}).click();
            expect(await field.getPostedValue(await requestPromise)).toBe('blue');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await expect(group.getByRole('radio', {name: 'Blue'})).toHaveAttribute('aria-checked', 'true');
            await expect(group.getByRole('radio', {name: 'Red'})).toHaveAttribute('aria-checked', 'false');

            await page.reload();
            await waitForLokiComponents(page);
            const reloadedGroup = field.root().getByRole('radiogroup');
            await expect(reloadedGroup.getByRole('radio', {name: 'Blue'})).toHaveAttribute('aria-checked', 'true');
            await expect(reloadedGroup.getByRole('radio', {name: 'Blue'})).toHaveAttribute('tabindex', '0');
        });

        test('selects options with the keyboard', async ({page}) => {
            const field = new TestField(page, 'button_group');
            const group = field.root().getByRole('radiogroup');
            const radio = (name: string) => group.getByRole('radio', {name});

            const pressKey = async (key: string, expectedValue: string, expectedLabel: string) => {
                const requestPromise = field.waitForUpdate();
                const responsePromise = page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));
                await page.keyboard.press(key);
                expect(await field.getPostedValue(await requestPromise)).toBe(expectedValue);
                await responsePromise;
                await expect(radio(expectedLabel)).toHaveAttribute('aria-checked', 'true');
                await expect(radio(expectedLabel)).toHaveAttribute('tabindex', '0');
                await expect(radio(expectedLabel)).toBeFocused();
            };

            await radio('Red').focus();
            await pressKey('ArrowRight', 'green', 'Green');
            await pressKey('End', 'blue', 'Blue');
            await pressKey('ArrowDown', 'red', 'Red');
            await pressKey('ArrowLeft', 'blue', 'Blue');
            await pressKey('Home', 'red', 'Red');
            await expect(radio('Green')).toHaveAttribute('tabindex', '-1');
        });

        test('does not select a disabled option', async ({page}) => {
            const field = new TestField(page, 'button_group');
            const group = field.root().getByRole('radiogroup');

            await group.getByRole('radio', {name: 'Black'}).click({force: true});
            await expect(group.getByRole('radio', {name: 'Black'})).toHaveAttribute('aria-checked', 'false');
            expect(await field.getComponentData('value')).toBe('red');
        });

        test('rejects a disabled option on the server', async ({page}) => {
            const field = new TestField(page, 'button_group');

            const requestPromise = field.waitForUpdate();
            await page.evaluate(() => {
                const component = (window as any).Alpine.store('LokiComponents').get('LokiFieldComponentsTestButtonGroup');
                component.value = 'black';
                component.post();
            });
            await requestPromise;
            await expect.poll(() => field.getErrorMessages()).toContain('This option is not available');
        });
    });
}
