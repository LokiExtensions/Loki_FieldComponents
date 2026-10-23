import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, TestField, THEMES, waitForLokiComponents} from './helpers/configure';

const CONFIG_PATH = 'loki_field_components/fields/use_required_block';

for (const theme of THEMES) {
    test.describe(`Loki Field Components required block (${theme})`, () => {
        test.afterAll(async ({browser}) => {
            const page = await browser.newPage({baseURL: process.env.TEST_URL, ignoreHTTPSErrors: true});
            await configure(page, {config: {[CONFIG_PATH]: 1}});
            await page.close();
        });

        test('shows a required block when enabled', async ({page}) => {
            await configure(page, {theme, config: {[CONFIG_PATH]: 1}});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            const required = new TestField(page, 'required');
            await expect(page.locator('label[for="' + required.elementId + '-field"] sub')).toHaveCount(1);

            const optional = new TestField(page, 'input');
            await expect(page.locator('label[for="' + optional.elementId + '-field"] sub')).toHaveCount(0);
        });

        test('hides the required block when disabled', async ({page}) => {
            await configure(page, {theme, config: {[CONFIG_PATH]: 0}});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            const required = new TestField(page, 'required');
            await expect(page.locator('label[for="' + required.elementId + '-field"]')).toBeVisible();
            await expect(page.locator('label[for="' + required.elementId + '-field"] sub')).toHaveCount(0);
        });
    });
}
