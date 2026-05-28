import { getServerSession } from "next-auth";
import { redirect } from "next/navigation";
import { AppShell } from "@/components/app-shell";
import { authOptions } from "@/lib/auth";

export default async function InternalLayout({ children }: { children: React.ReactNode }) {
  if (process.env.AUTH_REQUIRED === "true") {
    const session = await getServerSession(authOptions);

    if (!session) {
      redirect("/login");
    }
  }

  return <AppShell>{children}</AppShell>;
}
