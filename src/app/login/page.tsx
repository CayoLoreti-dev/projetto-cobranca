import { Building2 } from "lucide-react";
import { LoginForm } from "@/components/forms/login-form";

export default function LoginPage() {
  return (
    <main className="grid min-h-screen place-items-center px-4 py-8">
      <section className="w-full max-w-md rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div className="mb-6 flex items-center gap-3">
          <div className="flex h-11 w-11 items-center justify-center rounded-md bg-slate-950 text-white">
            <Building2 className="h-5 w-5" aria-hidden="true" />
          </div>
          <div>
            <h1 className="text-lg font-semibold text-slate-950">AP Vistoria Predial</h1>
            <p className="text-sm text-slate-500">Sistema interno de cobranças</p>
          </div>
        </div>
        <LoginForm />
      </section>
    </main>
  );
}
