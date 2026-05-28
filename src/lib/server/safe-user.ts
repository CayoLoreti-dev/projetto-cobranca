export function omitSenhaHash<T extends { senhaHash?: unknown }>(usuario: T): Omit<T, "senhaHash"> {
  const safeUsuario = { ...usuario };
  delete safeUsuario.senhaHash;
  return safeUsuario;
}
