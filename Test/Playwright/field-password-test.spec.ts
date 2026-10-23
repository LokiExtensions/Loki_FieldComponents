import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, TestField, THEMES, trackUpdates, waitForLokiComponents} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Field Components password fields (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('toggles the visibility of the password', async ({page}) => {
            const field = new TestField(page, 'password');

            await expect(field.field()).toHaveAttribute('type', 'password');

            await field.root().getByTitle('Show Password').click();
            await expect(field.field()).toHaveAttribute('type', 'text');
            await expect(field.root().getByTitle('Show Password')).toBeHidden();

            await field.root().getByTitle('Hide Password').click();
            await expect(field.field()).toHaveAttribute('type', 'password');
        });

        test('validates the password complexity step by step', async ({page}) => {
            const field = new TestField(page, 'password');
            const posted = trackUpdates(page, field.blockName);

            const steps = [
                {value: 'abc', error: 'Your password needs to have at least 8 characters.'},
                {value: 'abcdefgh', error: 'Your password needs to contain at least 2 numbers.'},
                {value: 'abcdefgh12', error: 'Your password needs to contain at least 2 non-alphanumeric characters.'},
            ];

            for (const step of steps) {
                await field.field().fill(step.value);
                await field.field().press('Tab');
                await expect.poll(() => field.getErrorMessages()).toEqual([step.error]);
                await expect(field.field()).toHaveAttribute('aria-invalid', 'true');
            }

            expect(posted, 'Invalid passwords are not posted').toEqual([]);
        });

        test('saves a complex password', async ({page}) => {
            const field = new TestField(page, 'password');

            const requestPromise = field.waitForUpdate();
            await field.field().fill('P@ssw0rd!42');
            await field.field().press('Tab');

            expect(await field.getPostedValue(await requestPromise)).toBe('P@ssw0rd!42');
            await expect.poll(() => field.getErrorMessages()).toEqual([]);
        });

        test('shows an error when repeated passwords do not match', async ({page}) => {
            const field = new TestField(page, 'password_repeat');
            const posted = trackUpdates(page, field.blockName);
            const repeat = field.root().getByLabel('Repeat password');

            await field.field().fill('P@ssw0rd!42');
            await field.field().press('Tab');
            await repeat.fill('Something else');
            await repeat.press('Tab');

            await expect.poll(() => field.getErrorMessages()).toContain('Passwords do not match');
            await expect(field.root()).toContainText('Passwords do not match');
            expect(posted).toEqual([]);
        });

        test('saves matching passwords', async ({page}) => {
            const field = new TestField(page, 'password_repeat');
            const repeat = field.root().getByLabel('Repeat password');

            const requestPromise = field.waitForUpdate();
            await field.field().fill('P@ssw0rd!42');
            await field.field().press('Tab');
            await repeat.fill('P@ssw0rd!42');
            await repeat.press('Tab');

            expect(await field.getPostedValue(await requestPromise)).toBe('P@ssw0rd!42');
        });

        test('toggles the visibility of both repeated passwords', async ({page}) => {
            const field = new TestField(page, 'password_repeat');
            const repeat = field.root().getByLabel('Repeat password');

            await expect(field.field()).toHaveAttribute('type', 'password');
            await expect(repeat).toHaveAttribute('type', 'password');

            await field.root().getByLabel('Show Password').click();
            await expect(field.field()).toHaveAttribute('type', 'text');
            await expect(repeat).toHaveAttribute('type', 'text');
        });
    });
}
