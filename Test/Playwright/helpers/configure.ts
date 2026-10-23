import {expect, Page, Request} from '@playwright/test';

export const THEMES = ['Magento/luma', 'Hyva/default-csp'];

export const TEST_PAGE_HANDLES = ['loki_field_components_index_test'];

export const HTML_ENDPOINT = '/loki_components/index/html';

export const isHyva = (theme: string): boolean => theme.startsWith('Hyva/');

export async function configure(page: Page, config: object) {
    const token = process.env.TEST_TOKEN;
    if (!token) {
        throw new Error('TEST_TOKEN is not set');
    }

    const response = await page.request.post(
        '/loki_functional_tests/index/configure?token=' + encodeURIComponent(token),
        {form: {config: JSON.stringify(config)}}
    );

    const body = await response.text();
    expect(response.status(), 'Configure response: ' + body).toBe(200);

    const data = JSON.parse(body);
    expect(data.error, 'Configure error').toBeUndefined();
    expect(data.result?.theme?.errors, 'Theme errors').toBeUndefined();
}

export async function waitForLokiComponents(page: Page) {
    await page.waitForFunction(() => {
        const Alpine = (window as any).Alpine;
        return Alpine
            && Alpine.store('LokiComponents')
            && Alpine.store('LokiComponents').getComponentIds().includes('LokiFieldComponentsTestInput');
    });
}

/**
 * Test field of the layout handle `loki_field_components_index_test`, for instance `password_repeat`
 */
export class TestField {
    readonly blockName: string;
    readonly elementId: string;
    readonly componentId: string;

    constructor(private page: Page, readonly name: string) {
        this.blockName = 'loki-field-components.test.' + name;
        this.elementId = 'loki-field-components-test-' + name.replace(/_/g, '-');
        this.componentId = 'LokiFieldComponentsTest' + name.split('_')
            .map(part => part.charAt(0).toUpperCase() + part.slice(1))
            .join('');
    }

    root() {
        return this.page.locator('#' + this.elementId);
    }

    field() {
        return this.page.locator('#' + this.elementId + '-field');
    }

    async getComponentData(property: string): Promise<any> {
        return this.page.evaluate(({componentId, property}) => {
            const component = (window as any).Alpine.store('LokiComponents').get(componentId);
            return JSON.parse(JSON.stringify(component[property] ?? null));
        }, {componentId: this.componentId, property});
    }

    async getErrorMessages(): Promise<string[]> {
        const messages = await this.getComponentData('messages') ?? [];
        return messages.filter((message: any) => message.type === 'error').map((message: any) => message.text);
    }

    waitForUpdate(): Promise<Request> {
        return this.page.waitForRequest(request => {
            if (!request.url().includes(HTML_ENDPOINT) || request.method() !== 'POST') {
                return false;
            }

            const updates = request.postDataJSON()?.updates ?? [];
            return updates.some((update: any) => update.blockName === this.blockName);
        });
    }

    async getPostedValue(request: Request): Promise<any> {
        const updates = request.postDataJSON()?.updates ?? [];
        return updates.find((update: any) => update.blockName === this.blockName)?.update;
    }
}

export function trackUpdates(page: Page, blockName: string): string[] {
    const posted: string[] = [];
    page.on('request', request => {
        if (!request.url().includes(HTML_ENDPOINT) || request.method() !== 'POST') {
            return;
        }

        const updates = request.postDataJSON()?.updates ?? [];
        for (const update of updates) {
            if (update.blockName === blockName) {
                posted.push(update.update);
            }
        }
    });

    return posted;
}
