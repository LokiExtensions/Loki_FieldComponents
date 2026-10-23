import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, HTML_ENDPOINT, TEST_PAGE_HANDLES, TestField, THEMES, waitForLokiComponents} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Field Components option fields (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('preselects the first enabled option of a select', async ({page}) => {
            const field = new TestField(page, 'select');

            await expect(field.field()).toHaveValue('red');
            await expect(field.field().locator('option[value="black"]')).toBeDisabled();
            expect(await field.getComponentData('value')).toBe('red');
        });

        test('saves a selected option', async ({page}) => {
            const field = new TestField(page, 'select');

            const requestPromise = field.waitForUpdate();
            await field.field().selectOption('blue');
            expect(await field.getPostedValue(await requestPromise)).toBe('blue');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await page.reload();
            await waitForLokiComponents(page);
            await expect(field.field()).toHaveValue('blue');
        });

        test('saves multiple selected options', async ({page}) => {
            const field = new TestField(page, 'select_multiple');

            const requestPromise = field.waitForUpdate();
            await field.field().selectOption(['green', 'blue']);
            expect(await field.getPostedValue(await requestPromise)).toEqual(['green', 'blue']);
        });

        test('preselects and saves a radio option', async ({page}) => {
            const field = new TestField(page, 'radio');
            const radioGroup = field.root().getByRole('radiogroup');
            const radio = (value: string) => radioGroup.locator('input[type="radio"][value="' + value + '"]');

            await expect(radio('red')).toBeChecked();
            await expect(radio('black')).toBeDisabled();

            const requestPromise = field.waitForUpdate();
            await radioGroup.locator('label', {hasText: 'Green'}).click();
            expect(await field.getPostedValue(await requestPromise)).toBe('green');
            await expect(radio('green')).toBeChecked();
            await expect(radio('red')).not.toBeChecked();
        });

        test('saves a checked checkbox', async ({page}) => {
            const field = new TestField(page, 'checkbox');
            const checkbox = field.root().getByRole('checkbox');

            await expect(field.root()).toContainText('Subscribe to test');
            await expect(checkbox).not.toBeChecked();

            const requestPromise = field.waitForUpdate();
            await checkbox.check();
            expect(await field.getPostedValue(await requestPromise)).toBe(true);
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await page.reload();
            await waitForLokiComponents(page);
            await expect(field.root().getByRole('checkbox')).toBeChecked();
        });

        test('shows an error when a required checkbox is unchecked', async ({page}) => {
            const field = new TestField(page, 'checkbox_required');
            const checkbox = field.root().getByRole('checkbox');

            const requestPromise = field.waitForUpdate();
            await checkbox.check();
            await requestPromise;
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await field.root().getByRole('checkbox').uncheck();
            await expect.poll(() => field.getErrorMessages()).toContain('Value is required');
            await expect(field.root().locator('[data-type="field"]').first()).toHaveAttribute('aria-invalid', 'true');
        });

        test('saves a switch', async ({page}) => {
            const field = new TestField(page, 'switch');
            const toggle = field.root().getByRole('switch');

            await expect(field.root()).toContainText('Enable test');

            const requestPromise = field.waitForUpdate();
            await toggle.check({force: true});
            expect(await field.getPostedValue(await requestPromise)).toBe(true);
        });
    });
}
