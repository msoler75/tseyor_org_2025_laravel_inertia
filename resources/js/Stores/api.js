
export function getApiUrl() {
    const page = usePage()
    return page?.props?.api_url || '';
}
