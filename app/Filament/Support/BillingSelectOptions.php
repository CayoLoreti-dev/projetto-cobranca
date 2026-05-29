<?php

namespace App\Filament\Support;

use App\Models\Boleto;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Parcela;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BillingSelectOptions
{
    /**
     * @return array<string, string>
     */
    public static function clientes(?string $search = null): array
    {
        return Cliente::query()
            ->when(self::hasSearch($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $like = self::like($search);

                    $query
                        ->where('nome', 'like', $like)
                        ->orWhere('documento', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('nome')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Cliente $cliente): array => [
                $cliente->id => self::clienteLabel($cliente),
            ])
            ->all();
    }

    public static function clienteLabelForId(mixed $id): ?string
    {
        if (blank($id)) {
            return null;
        }

        $cliente = Cliente::query()->find($id);

        return $cliente ? self::clienteLabel($cliente) : null;
    }

    public static function clienteLabel(Cliente $cliente): string
    {
        return "{$cliente->nome} | {$cliente->documento}";
    }

    /**
     * @return array<string, string>
     */
    public static function cobrancas(?string $search = null): array
    {
        return Cobranca::query()
            ->with('cliente')
            ->when(self::hasSearch($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $like = self::like($search);

                    $query
                        ->where('codigo', 'like', $like)
                        ->orWhere('categoria', 'like', $like)
                        ->orWhereHas('cliente', function (Builder $query) use ($like): void {
                            $query
                                ->where('nome', 'like', $like)
                                ->orWhere('documento', 'like', $like);
                        });
                });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Cobranca $cobranca): array => [
                $cobranca->id => self::cobrancaLabel($cobranca),
            ])
            ->all();
    }

    public static function cobrancaLabelForId(mixed $id): ?string
    {
        if (blank($id)) {
            return null;
        }

        $cobranca = Cobranca::query()->with('cliente')->find($id);

        return $cobranca ? self::cobrancaLabel($cobranca) : null;
    }

    public static function cobrancaLabel(Cobranca $cobranca): string
    {
        $cliente = $cobranca->cliente?->nome ?? 'Cliente nao informado';
        $vencimento = $cobranca->data_vencimento_principal?->format('d/m/Y') ?? 'sem vencimento';

        return "{$cliente} | {$cobranca->codigo} | ".self::money($cobranca->valor_total)." | venc. {$vencimento}";
    }

    /**
     * @return array<string, string>
     */
    public static function parcelas(?string $search = null): array
    {
        return Parcela::query()
            ->with('cobranca.cliente')
            ->when(self::hasSearch($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $like = self::like($search);

                    if (ctype_digit((string) $search)) {
                        $query->where('numero', (int) $search);
                    }

                    $query->orWhereHas('cobranca', function (Builder $query) use ($like): void {
                        $query
                            ->where('codigo', 'like', $like)
                            ->orWhereHas('cliente', function (Builder $query) use ($like): void {
                                $query
                                    ->where('nome', 'like', $like)
                                    ->orWhere('documento', 'like', $like);
                            });
                    });
                });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Parcela $parcela): array => [
                $parcela->id => self::parcelaLabel($parcela),
            ])
            ->all();
    }

    public static function parcelaLabelForId(mixed $id): ?string
    {
        if (blank($id)) {
            return null;
        }

        $parcela = Parcela::query()->with('cobranca.cliente')->find($id);

        return $parcela ? self::parcelaLabel($parcela) : null;
    }

    public static function parcelaLabel(Parcela $parcela): string
    {
        $cliente = $parcela->cobranca?->cliente?->nome ?? 'Cliente nao informado';
        $codigo = $parcela->cobranca?->codigo ?? 'sem cobranca';
        $vencimento = $parcela->vencimento?->format('d/m/Y') ?? 'sem vencimento';

        return "{$cliente} | {$codigo} | parcela {$parcela->numero} | ".self::money($parcela->valor)." | venc. {$vencimento}";
    }

    /**
     * @return array<string, string>
     */
    public static function boletos(?string $search = null): array
    {
        return Boleto::query()
            ->with(['cobranca.cliente', 'parcela'])
            ->when(self::hasSearch($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $like = self::like($search);

                    $query
                        ->where('linha_digitavel', 'like', $like)
                        ->orWhere('codigo_barras', 'like', $like)
                        ->orWhere('status', 'like', $like)
                        ->orWhereHas('cobranca', function (Builder $query) use ($like): void {
                            $query
                                ->where('codigo', 'like', $like)
                                ->orWhereHas('cliente', function (Builder $query) use ($like): void {
                                    $query
                                        ->where('nome', 'like', $like)
                                        ->orWhere('documento', 'like', $like);
                                });
                        });
                });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Boleto $boleto): array => [
                $boleto->id => self::boletoLabel($boleto),
            ])
            ->all();
    }

    public static function boletoLabelForId(mixed $id): ?string
    {
        if (blank($id)) {
            return null;
        }

        $boleto = Boleto::query()->with(['cobranca.cliente', 'parcela'])->find($id);

        return $boleto ? self::boletoLabel($boleto) : null;
    }

    public static function boletoLabel(Boleto $boleto): string
    {
        $cliente = $boleto->cobranca?->cliente?->nome ?? 'Cliente nao informado';
        $codigo = $boleto->cobranca?->codigo ?? 'sem cobranca';
        $vencimento = $boleto->vencimento?->format('d/m/Y') ?? 'sem vencimento';
        $identificacao = $boleto->linha_digitavel ?: 'ID '.substr((string) $boleto->id, 0, 8);

        return "{$cliente} | {$codigo} | {$identificacao} | ".self::money($boleto->valor)." | venc. {$vencimento}";
    }

    /**
     * @return array<int, string>
     */
    public static function usuarios(?string $search = null): array
    {
        return User::query()
            ->when(self::hasSearch($search), function (Builder $query) use ($search): void {
                $like = self::like($search);

                $query
                    ->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (User $user): array => [
                $user->id => self::usuarioLabel($user),
            ])
            ->all();
    }

    public static function usuarioLabelForId(mixed $id): ?string
    {
        if (blank($id)) {
            return null;
        }

        $user = User::query()->find($id);

        return $user ? self::usuarioLabel($user) : null;
    }

    public static function usuarioLabel(User $user): string
    {
        return "{$user->name} | {$user->email}";
    }

    private static function hasSearch(?string $search): bool
    {
        return trim((string) $search) !== '';
    }

    private static function like(?string $search): string
    {
        return '%'.trim((string) $search).'%';
    }

    private static function money(mixed $value): string
    {
        return 'R$ '.number_format((float) $value, 2, ',', '.');
    }
}
