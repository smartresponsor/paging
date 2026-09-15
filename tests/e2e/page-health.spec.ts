import { expect, test } from '@playwright/test';

test('Page health endpoint is reachable', async ({ request }) => {
  const response = await request.get('/_page/health');

  expect(response.ok()).toBeTruthy();
  expect(await response.json()).toMatchObject({ component: 'Paging' });
});
