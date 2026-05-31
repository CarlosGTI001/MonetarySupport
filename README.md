# MonetarySupport

## Manual de instalacion en hosting
1. Sube todo el proyecto al hosting (por ejemplo a `/home/usuario/MonetarySupport` o dentro de `public_html/MonetarySupport`).
2. Configura el document root del dominio para que apunte a la carpeta `public` del proyecto (ej. `/home/usuario/MonetarySupport/public`).
3. Asegura permisos de escritura para `storage/` porque se usa SQLite (`storage/database.sqlite`).
4. Verifica que el hosting tenga PHP con PDO y PDO SQLite habilitados.
5. Si no puedes cambiar el document root, usa el `.htaccess` del proyecto para redirigir todo a `public/`.

## Subdominio con carpeta dentro de public
1. Crea el subdominio en el panel. Normalmente se crea una carpeta dentro de `public_html` (ej. `public_html/mi-subdominio`).
2. Sube el proyecto dentro de esa carpeta (ej. `public_html/mi-subdominio/MonetarySupport`).
3. Cambia el document root del subdominio a la carpeta `public` del proyecto (ej. `public_html/mi-subdominio/MonetarySupport/public`).
4. Asegura permisos de escritura para `storage/` igual que en el caso principal.
5. Si el document root del subdominio debe quedar en la carpeta raiz, aplica el `.htaccess` incluido para enviar todo a `public/`.
