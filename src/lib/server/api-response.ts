import { NextResponse } from "next/server";
import { ZodError } from "zod";

export function ok<T>(data: T, init?: ResponseInit) {
  return NextResponse.json({ ok: true, data }, init);
}

export function created<T>(data: T) {
  return ok(data, { status: 201 });
}

export function fail(message: string, status = 400, details?: unknown) {
  return NextResponse.json(
    {
      ok: false,
      error: {
        message,
        details,
      },
    },
    { status },
  );
}

export function handleRouteError(error: unknown) {
  if (error instanceof ZodError) {
    return fail("Dados inválidos.", 400, error.flatten());
  }

  if (error instanceof Error) {
    return fail(error.message, 500);
  }

  return fail("Erro inesperado.", 500);
}

export function parseSearchParams(request: Request) {
  return Object.fromEntries(new URL(request.url).searchParams.entries());
}
