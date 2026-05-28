export async function readJson(request: Request) {
  return request.json().catch(() => null);
}

export function emptyToNull(value?: string | null) {
  return value && value.trim().length > 0 ? value : null;
}
