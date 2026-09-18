-- Esquema para el clon PHP del Manual de Registración.
-- Correr una sola vez contra una base nueva (ej. rpba_registracion).

CREATE TABLE IF NOT EXISTS categorias (
  id          text PRIMARY KEY,
  titulo      text NOT NULL,
  descripcion text NOT NULL,
  orden       integer NOT NULL
);

CREATE TABLE IF NOT EXISTS capitulos (
  id                        serial PRIMARY KEY,
  slug                      text NOT NULL UNIQUE,
  titulo                    text NOT NULL,
  codigo_acto               text,
  categoria_id              text NOT NULL REFERENCES categorias(id) ON UPDATE CASCADE,
  pagina                    integer NOT NULL,
  clasificacion_ingreso     text[] NOT NULL,
  art2_ley17801             text NOT NULL,
  rubros_folio_real         text[] NOT NULL,
  requiere_cert_dominio     boolean NOT NULL,
  requiere_insc_provisional boolean NOT NULL,
  requiere_cert_catastral   boolean,
  descripcion               text NOT NULL,
  documentos                text[] NOT NULL,
  modelo_asiento            text NOT NULL,
  observaciones             text,
  orden                     integer NOT NULL UNIQUE
);

CREATE INDEX IF NOT EXISTS idx_capitulos_categoria ON capitulos(categoria_id);
