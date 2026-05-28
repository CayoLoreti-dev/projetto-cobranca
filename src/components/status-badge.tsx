import { statusTone } from "@/lib/constants";
import { cn } from "@/lib/utils";

export function StatusBadge({ status, label }: { status: string; label?: string }) {
  return (
    <span
      className={cn(
        "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset",
        statusTone[status] ?? "bg-slate-100 text-slate-700 ring-slate-200",
      )}
    >
      {label ?? status}
    </span>
  );
}
