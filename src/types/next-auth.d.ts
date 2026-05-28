import type { DefaultSession, DefaultUser } from "next-auth";

declare module "next-auth" {
  interface Session {
    user?: DefaultSession["user"] & {
      id: string;
      perfil: string;
    };
  }

  interface User extends DefaultUser {
    perfil?: string;
  }
}

declare module "next-auth/jwt" {
  interface JWT {
    perfil?: string;
  }
}
