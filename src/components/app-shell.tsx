"use client";

import {
  BarChart3,
  CalendarDays,
  ClipboardList,
  FileClock,
  FileText,
  History,
  LayoutDashboard,
  ListTodo,
  LogOut,
  Receipt,
  Settings,
  UserCog,
  UserRound,
  UsersRound,
  WalletCards,
} from "lucide-react";
import { signOut, useSession } from "next-auth/react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

const navItems = [
  { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
  { href: "/clientes", label: "Clientes", icon: UsersRound },
  { href: "/usuarios", label: "Usuários", icon: UserCog },
  { href: "/minhas-demandas", label: "Minhas demandas", icon: ListTodo },
  { href: "/cobrancas/nova", label: "Nova cobrança", icon: WalletCards },
  { href: "/parcelas", label: "Parcelas", icon: ClipboardList },
  { href: "/boletos", label: "Boletos", icon: Receipt },
  { href: "/interacoes", label: "Histórico", icon: History },
  { href: "/agenda", label: "Agenda", icon: CalendarDays },
  { href: "/relatorios", label: "Relatórios", icon: BarChart3 },
  { href: "/auditoria", label: "Auditoria", icon: FileClock },
  { href: "/configuracoes", label: "Configurações", icon: Settings },
];

export function AppShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const { data: session } = useSession();

  return (
    <div className="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
      <aside className="border-b border-slate-200 bg-white lg:sticky lg:top-0 lg:h-screen lg:border-b-0 lg:border-r">
        <div className="flex h-full flex-col">
          <div className="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
            <Link href="/dashboard" className="flex min-w-0 items-center gap-3">
              <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-slate-950 text-white">
                <FileText className="h-5 w-5" aria-hidden="true" />
              </div>
              <div className="min-w-0">
                <p className="truncate text-sm font-semibold text-slate-950">AP Vistoria</p>
                <p className="truncate text-xs text-slate-500">Cobranças internas</p>
              </div>
            </Link>
          </div>

          <nav className="flex gap-1 overflow-x-auto px-3 py-3 lg:flex-1 lg:flex-col lg:overflow-x-visible">
            {navItems.map((item) => {
              const Icon = item.icon;
              const active = pathname === item.href || pathname.startsWith(`${item.href}/`);

              return (
                <Link
                  key={item.href}
                  href={item.href}
                  className={cn(
                    "inline-flex h-10 shrink-0 items-center gap-3 rounded-md px-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950",
                    active && "bg-slate-950 text-white hover:bg-slate-900 hover:text-white",
                  )}
                  title={item.label}
                >
                  <Icon className="h-4 w-4" aria-hidden="true" />
                  <span>{item.label}</span>
                </Link>
              );
            })}
          </nav>

          <div className="hidden border-t border-slate-200 p-4 lg:block">
            <div className="flex items-center gap-3">
              <div className="flex h-9 w-9 items-center justify-center rounded-md bg-slate-100 text-slate-700">
                <UserRound className="h-4 w-4" aria-hidden="true" />
              </div>
              <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-medium text-slate-950">{session?.user?.name ?? "Usuário local"}</p>
                <p className="truncate text-xs text-slate-500">{session?.user?.email ?? "sem sessão ativa"}</p>
              </div>
              <Button
                type="button"
                variant="ghost"
                size="icon"
                title="Sair"
                onClick={() => signOut({ callbackUrl: "/login" })}
              >
                <LogOut className="h-4 w-4" aria-hidden="true" />
              </Button>
            </div>
          </div>
        </div>
      </aside>

      <main className="min-w-0 px-4 py-5 sm:px-6 lg:px-8 lg:py-7">{children}</main>
    </div>
  );
}
