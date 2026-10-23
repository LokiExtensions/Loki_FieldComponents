import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, HTML_ENDPOINT, TEST_PAGE_HANDLES, TestField, THEMES, waitForLokiComponents} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Field Components input field (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('renders an input with label, placeholder and help text', async ({page}) => {
            const field = new TestField(page, 'input');

            await expect(field.root()).toHaveAttribute('x-data', 'LokiFieldComponent');
            await expect(field.field()).toHaveAttribute('type', 'text');
            await expect(field.field()).toHaveAttribute('placeholder', 'Type something');
            await expect(page.locator('label[for="' + field.elementId + '-field"]')).toContainText('Test input');
            await expect(field.root()).toContainText('Some help text');
        });

        test('saves a changed value and keeps it after reload', async ({page}) => {
            const field = new TestField(page, 'input');

            const requestPromise = field.waitForUpdate();
            await field.field().fill('Hello Playwright');
            await field.field().press('Tab');

            const request = await requestPromise;
            expect(await field.getPostedValue(request)).toBe('Hello Playwright');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await page.reload();
            await waitForLokiComponents(page);
            await expect(field.field()).toHaveValue('Hello Playwright');
        });

        test('marks the field busy while saving', async ({page}) => {
            const field = new TestField(page, 'input');

            let release: () => void = () => {};
            const released = new Promise<void>(resolve => release = resolve);
            await page.route('**' + HTML_ENDPOINT + '**', async route => {
                await released;
                await route.continue();
            });

            await field.field().fill('Slow value');
            await field.field().press('Tab');

            await expect(field.field()).toHaveAttribute('aria-busy', 'true');
            release();
            await expect(field.field()).not.toHaveAttribute('aria-busy');
        });

        test('shows an error when a required field is emptied', async ({page}) => {
            const field = new TestField(page, 'required');

            await field.field().fill('Something');
            await field.field().press('Tab');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await field.field().fill('');
            await field.field().press('Tab');

            await expect.poll(() => field.getErrorMessages()).toContain('Value is required');
            await expect(field.root()).toContainText('Value is required');
            await expect(field.field()).toHaveAttribute('aria-invalid', 'true');
        });

        test('shows a custom required message', async ({page}) => {
            const field = new TestField(page, 'required_custom');

            await field.field().fill('Something');
            await field.field().press('Tab');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await field.field().fill('   ');
            await field.field().press('Tab');

            await expect.poll(() => field.getErrorMessages()).toContain('Please fill in this test field');
            await expect(field.field()).toHaveAttribute('aria-invalid', 'true');
        });

        test('clears the error once a valid value is entered', async ({page}) => {
            const field = new TestField(page, 'required');

            await field.field().fill('Something');
            await field.field().press('Tab');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await field.field().fill('');
            await field.field().press('Tab');
            await expect(field.field()).toHaveAttribute('aria-invalid', 'true');

            const requestPromise = field.waitForUpdate();
            await field.field().fill('Fixed');
            await field.field().press('Tab');
            await requestPromise;

            await expect(field.field()).not.toHaveAttribute('aria-invalid');
            await expect.poll(() => field.getErrorMessages()).toEqual([]);
        });

        test('renders a disabled field', async ({page}) => {
            const field = new TestField(page, 'disabled');

            await expect(field.field()).toBeDisabled();
            await expect(field.field()).toHaveAttribute('autocomplete', 'off');
        });

        test('formats a masked field', async ({page}) => {
            const field = new TestField(page, 'masked');

            await field.field().pressSequentially('1234ab');
            await expect(field.field()).toHaveValue('1234 ab');
        });
    });
}
