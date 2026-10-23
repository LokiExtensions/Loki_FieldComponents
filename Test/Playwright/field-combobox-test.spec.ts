import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, HTML_ENDPOINT, TEST_PAGE_HANDLES, TestField, THEMES, waitForLokiComponents} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Field Components combobox (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('shows the enabled options when focused', async ({page}) => {
            const field = new TestField(page, 'combobox');
            const combobox = field.root().getByRole('combobox');
            const listbox = field.root().getByRole('listbox');

            await expect(combobox).toHaveAttribute('placeholder', 'Choose a color');
            await expect(listbox).toBeHidden();

            await combobox.click();
            await expect(listbox).toBeVisible();
            await expect(combobox).toHaveAttribute('aria-expanded', 'true');
            await expect(listbox.getByRole('option')).toHaveText(['Red', 'Green', 'Blue']);

            await combobox.press('Escape');
            await expect(listbox).toBeHidden();
        });

        test('filters the options while typing', async ({page}) => {
            const field = new TestField(page, 'combobox');
            const combobox = field.root().getByRole('combobox');
            const listbox = field.root().getByRole('listbox');

            await combobox.fill('bl');
            await expect(listbox.getByRole('option')).toHaveText(['Blue']);

            await combobox.fill('xyz');
            await expect(listbox).toContainText('No results found');
        });

        test('selects an option with the keyboard and saves it', async ({page}) => {
            const field = new TestField(page, 'combobox');
            const combobox = field.root().getByRole('combobox');

            await combobox.click();
            const requestPromise = field.waitForUpdate();
            await combobox.press('ArrowDown');
            await combobox.press('Enter');

            expect(await field.getPostedValue(await requestPromise)).toBe('green');
            await expect(field.root().getByRole('combobox')).toHaveValue('Green');
            await page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));

            await page.reload();
            await waitForLokiComponents(page);
            await expect(field.root().getByRole('combobox')).toHaveValue('Green');
        });

        test('selects an option with the mouse and saves it', async ({page}) => {
            const field = new TestField(page, 'combobox');
            const combobox = field.root().getByRole('combobox');

            await combobox.click();
            const requestPromise = field.waitForUpdate();
            await field.root().getByRole('option', {name: 'Blue'}).click();

            expect(await field.getPostedValue(await requestPromise)).toBe('blue');
            await expect(field.root().getByRole('combobox')).toHaveValue('Blue');
        });

        test('opens and closes the list with the toggle button', async ({page}) => {
            const field = new TestField(page, 'combobox');
            const listbox = field.root().getByRole('listbox');
            const toggle = field.root().getByRole('button', {name: 'Toggle options'});

            await toggle.click();
            await expect(listbox).toBeVisible();
            await expect(field.root().getByRole('combobox')).toBeFocused();

            await toggle.click();
            await expect(listbox).toBeHidden();
        });
    });
}
