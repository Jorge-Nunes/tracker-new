# AGENTS.md

Comunique-se com o usuário e escreva documentos/artefatos deste repositório em **pt-BR**.

## Visão geral

`/var/www` é um **monorepo git** (**tracker-new**, usuário `Jorge-Nunes`) que entrega a plataforma **Tarkan** (rastreamento de veículos). **Substitui os repos antigos da conta `riccefarias`/`rickfarias`** (deploy keys `basic`/`plus` foram removidas do `~/.ssh/config`; backup em `/root/backup_git_20260808_174338/`).

| Caminho | O quê é | Origem git |
|---|---|---|
| raiz `/var/www` | **tracker-new** — monorepo git (branch `main`) | `git@github.com:Jorge-Nunes/tracker-new.git` |
| `tarkan-api/` | **Tarkan-Plus** — backend Laravel 8 (PHP ^7.3\|^8.0), API em `routes/api.php` — **rastreado pelo tracker-new** | — |
| `tarkan-desktop-src/` | Fonte Vue3 do frontend (sem `node_modules`) — **rastreado pelo tracker-new** | — |
| `tarkan-desktop/` | **Tarkan-Basic** — SPA Vue3 **compilada** (artefato de distribuição); **NÃO** está no tracker-new — tem `.git` próprio legado | repo legado da conta anterior |
| `assets/` | Upload inicial → `storage/app/assets/default/` (branding + `config.json`) | — |
| `_VHOST/` | vhosts nginx de referência (8080 desktop / 8090 api) | — |
| `openapi.yaml` | OpenAPI oficial do Traccar (v6.14.5) — contrato do servidor proxiado | — |
| `env`, `db_tarkan.sql`, `Documentacao_Interna_Tarkan.md`, `AGENTS.md` | Modelo de `.env`, schema MySQL, manual de instalação/licença, doc de agentes (todos fora do tracker-new exceto `AGENTS.md`) | — |

**Regra de ouro:** nunca edite `tarkan-desktop/` (JS/CSS/HTML minificado). O fonte é `tarkan-desktop-src/`; o build é copiado para `tarkan-desktop/` e publicado.

## Deploy e git (substituiu as deploy keys da conta antiga)

- **O acesso git agora é `github.com` normal** via chave SSH pessoal `~/.ssh/tracker-new` (ed25519, identidade `Jorge-Nunes <jcvn@jcvn.com.br>`). Os hosts `git@basic.github.com`/`git@plus.github.com` (deploy keys RSA por cliente da conta `riccefarias`) **foram removidos** — backup do antigo `~/.ssh/config` e das chaves em `/root/backup_git_20260808_174338/`.
- **Fluxo do monorepo:** editar → `git add -A` → commit → `git push origin main` na raiz (`/var/www`). O `.gitignore` raiz só rastreia `tarkan-desktop-src/` e `tarkan-api/` (e `AGENTS.md`); o resto (`tarkan-desktop/`, `.env`, `db_tarkan.sql`, storage etc.) fica de fora.
- Fluxo do front: editar `tarkan-desktop-src/` → `yarn build` → copiar `dist/*` para `tarkan-desktop/` → commit/push no repo legado do `tarkan-desktop/` (separado).
- Fluxo do backend: editar `tarkan-api/` → commit/push no tracker-new. No servidor: `composer install`, `chmod -R 0777 storage`, importar `db_tarkan.sql` e configurar `.env`.
- ⚠️ O GitHub **secret scanning** bloqueia push com chaves de API no código — as chaves são externalizadas (ver seção abaixo).

## Comandos

### Backend (`tarkan-api/`)
```bash
composer install
cp /var/www/env .env        # repo não tem .env (gitignored); /var/www/env é o modelo
php artisan key:generate
vendor/bin/phpunit          # só stubs do Laravel — não há suite real
```
- Não há `.env` presente; a API não sobe até criar um. Ajustes de banco são feitos no `.env`.
- Estilo PHP via presets do StyleCI (`laravel`, PSR-12) — não há lint local.

### Frontend (`tarkan-desktop-src/`)
```bash
yarn install
yarn serve                  # dev desktop (main.js)
yarn build                  # build de produção → dist/
yarn mbuild                 # build mobile → dist-mobile/ (main-mobile.js)
yarn mcserve / yarn mserve  # dev mobile (main-mobile-client.js / main-mobile.js)
yarn lint                   # eslint vue3-essential (única checagem; sem Prettier)
```
- Há **3 entrypoints**: `main.js` (desktop), `main-mobile.js` e `main-mobile-client.js`.
- `yarn build` sempre dispara o `webpack-bundle-analyzer` (plugin ativo em `vue.config.js`) — abrir a análise é esperado.
- `test/` é artefato antigo de build UMD (demo), ignore.

## Arquitetura (o que o código não deixa óbvio)

- **O backend é uma retaguarda/proxy para o Traccar**, não o sistema de tracking. `app/Tarkan/traccarConnector.php` monta o host do Traccar em runtime: usa `TARKAN_HOST/TARKAN_USERNAME/TARKAN_PASSWORD` do `.env` se presentes; senão usa `storage/app/assets/**/config.json` (credenciais admin) + headers `tarkan-domain` e `traccar-host` setados pelo nginx.
- **Headers de runtime (setados por `_VHOST/tarkan_common`):** `tarkan-domain`, `traccar-host`, `tarkan-token`, `tarkan-code`. A API filtra dados por esses headers (ex.: `UserLog` por `tarkan-domain`). Sem nginx, envie esses headers manualmente.
- **Customização por cliente:** `PUT /theme` e `/theme/upload` gravam branding em `storage/app/assets/{ip}/{domain}/assets/custom/` (`colors.js`, `config.js`, `manifest.json`, ícones). Nginx serve `/assets` com fallback para `default`. O `tarkan-desktop/tarkan/assets/custom/` é o branding default commitado no SPA.
- **`storage/app/assets/default/config.json` guarda credenciais de admin do Traccar usadas em runtime — tratar como segredo**; não copie os valores reais para commits ou docs.
- `ENABLE_DB_INTRUSIVE=true` no `.env` troca as rotas `/api/users` para os controllers `OMC` (acesso direto ao banco) em vez do proxy Traccar.
- Rotas `/tarkan/*` chegam na API sem o prefixo (nginx faz `rewrite`); `location /assets` serve `storage/app/assets/`.

## Chaves de API externalizadas (Mapbox, Firebase, Google Maps)

As chaves **não ficam no código commitado** (o GitHub secret scanning rejeita push que as contenha). Todas são lidas em runtime:

| Chave | Uso | Onde é lida | Onde fica o valor real (gitignored) |
|---|---|---|---|
| **Mapbox** (`pk.…`) | Tiles do mapa (`kore-map.vue` → `availableMaps`) | `CONFIG.mapboxToken` (global carregado de `/tarkan/assets/custom/config.js`) | `storage/app/assets/**/assets/custom/config.js` (runtime por cliente) |
| **Firebase + vapid** | Push notifications (`push.js`, `firebase-messaging-sw.js`, `src/firebase.js`) | `CONFIG.firebase` e `CONFIG.vapidKey`; o SW faz `importScripts('/tarkan/assets/custom/config.js')` | `storage/app/assets/**/assets/custom/config.js` |
| **Google Maps Static** | PDF de resumo de viagem (`resume.blade.php`) | `config('services.google_maps.key')` | `GOOGLE_MAPS_KEY` no `.env` do backend (modelo: `/var/www/env`) |

- O `CONFIG` global é injetado no `<head>` do `index.html` via `<script src="/tarkan/assets/custom/config.js">`, servido pelo nginx com fallback para `public/tarkan/assets/custom/config.js` (default commitado com placeholders vazios).
- `config/services.php` expõe `google_maps.key` ← `env('GOOGLE_MAPS_KEY')`.
- Para alterar: edite o `config.js` runtime do cliente (`PUT /theme` grava nesse diretório) ou o `.env` + `php artisan config:clear`.
- Check rápido antes de push: `git grep -E 'pk\.eyJ|AIzaSy|BKmcr'` deve retornar **vazio**.

## Referência da API do Traccar (`openapi.yaml`)

- `/var/www/openapi.yaml` é o **OpenAPI oficial do Traccar** (3.1, versão `6.14.5`), baixado de `raw.githubusercontent.com/traccar/traccar/master/openapi.yaml` — fonte de verdade para endpoints/modelos do servidor proxiado. Atualize-o quando o Traccar de referência subir de versão.
- Os métodos de `app/Tarkan/traccarConnector.php` (ex.: `getDevices`, `postSession`, `sendCommand`, `linkObjects`, `getRoute`) espelham os paths do spec (`/devices`, `/session`, `/commands`, `/permissions`, `/reports/route`…). Controllers repassam para esses métodos, não chamam o Traccar direto.
- Autenticação (igual ao `security` do spec, BasicAuth + ApiKey): as requisições usam `withBasicAuth()` com as credenciais do runtime (`config.json`/`TARKAN_*`) **ou** repassam o `Cookie` (JSESSIONID) do cliente logado ao Traccar via `['h' => ['Cookie' => ...]]`.
- O spec assume `http://{host}:{port}/api` (porta default **8082** — mesmo default do `$traccarHost` no vhost). Cada cliente roda sua própria versão do Traccar, então o spec pode divergir da produção — confira o endpoint no spec e no código antes de alterar contratos.

## Gotchas verificados

- `app/Http/Middleware/FixTarkan.php` é importado no `Kernel.php` mas **não está registrado** na lista de middleware (código morto).
- Testes do backend são os stubs do Laravel (`tests/ExampleTest.php`) — não confie neles como cobertura.
- `productionSourceMap: true` em `vue.config.js` — source maps vão para o build de produção.
- UI e textos são em **pt-BR** (mobile usa `src/lang/pt-BR`); o i18n default do vue-cli é `en-US` — não altere sem motivo.
- `kore-map.vue` tem uma inconsistência histórica de ids de mapa (MapBox/OSM); o `CONFIG.mapboxToken` externo pode não cobrir todos os caminhos — ao mexer no seletor de mapa, valide `changeMap`/`selectedMap`/`availableMaps` juntos.
- `AGENTS.md` da raiz é rastreado pelo tracker-new (adicionado ao `.gitignore` com `!/AGENTS.md`); os demais arquivos da raiz (`env`, `db_tarkan.sql`) não sobem.
