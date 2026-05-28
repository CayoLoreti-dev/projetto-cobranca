import type { TipoCliente } from "@/generated/prisma/client";
import { notFound } from "next/navigation";
import { BoletosSection } from "@/components/boletos/boletos-section";
import { listarBoletosPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

const categorias: Record<
  string,
  {
    title: string;
    description: string;
    tipoCliente: TipoCliente;
  }
> = {
  "pessoa-fisica": {
    title: "Boletos de pessoa física",
    description: "Clientes PF",
    tipoCliente: "PF",
  },
  "pessoa-juridica": {
    title: "Boletos de pessoa jurídica",
    description: "Clientes PJ",
    tipoCliente: "PJ",
  },
  condominios: {
    title: "Boletos de condomínios",
    description: "Clientes condomínio",
    tipoCliente: "CONDOMINIO",
  },
};

type PageProps = { params: Promise<{ categoria: string }> | { categoria: string } };

export default async function BoletosCategoriaPage({ params }: PageProps) {
  const { categoria } = await params;
  const config = categorias[categoria];

  if (!config) {
    notFound();
  }

  const boletos = await listarBoletosPage(config.tipoCliente);

  return (
    <BoletosSection
      title={config.title}
      description={config.description}
      activeKey={categoria}
      boletos={boletos}
    />
  );
}
