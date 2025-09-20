# Funcionalidad de Exportación a Excel - Sistema de Pedidos

## Descripción
Se ha implementado una funcionalidad completa para exportar el listado de pedidos a formato Excel (.xlsx) con filtros aplicables y formato profesional.

## Características Implementadas

### 1. Botón de Descarga Excel
- **Ubicación**: Barra de acciones principal del sistema de pedidos
- **Estilo**: Botón verde con gradiente y efectos hover
- **Icono**: FontAwesome file-excel
- **Tooltip**: "Descargar listado de pedidos en formato Excel"

### 2. Filtros Aplicables
La exportación respeta todos los filtros activos:
- **Estado del pedido**: pendiente, aprobado, rechazado, en_proceso, completado, cancelado
- **Rango de fechas**: fecha inicio y fecha fin
- **Búsqueda**: texto libre que busca en folio, observaciones y usuario

### 3. Contenido del Excel
El archivo Excel generado incluye:

#### Encabezados principales:
- Folio del pedido
- Estado (con colores)
- Fecha de creación
- Usuario
- Sucursal
- Total estimado
- Prioridad
- Observaciones
- Lista de productos

#### Información adicional:
- Filtros aplicados al momento de la exportación
- Fecha y hora de generación
- Total de pedidos exportados

### 4. Formato y Estilos
- **Encabezados**: Fondo azul con texto blanco y bordes
- **Estados con colores**: Cada estado tiene un color distintivo
- **Bordes**: Todas las celdas tienen bordes definidos
- **Alineación**: Texto centrado en encabezados, alineado en datos
- **Ancho de columnas**: Optimizado para mejor visualización

## Archivos Modificados

### 1. `Pedidos.php`
- Agregado botón "Descargar Excel" en la barra de acciones
- Estilos CSS específicos para el botón
- Tooltip informativo

### 2. `js/pedidos-modern.js`
- Función `descargarExcel()` con efectos visuales
- Validación de datos antes de exportar
- Estados de carga y feedback al usuario

### 3. `api/exportar_pedidos_excel.php` (NUEVO)
- Endpoint para generar archivo Excel
- Consulta SQL con filtros aplicables
- Generación de archivo usando PhpSpreadsheet
- Headers HTTP para descarga automática

## Dependencias Requeridas

### PhpSpreadsheet
El sistema utiliza la librería PhpSpreadsheet para generar archivos Excel. Asegúrate de que esté instalada:

```bash
composer require phpoffice/phpspreadsheet
```

### Estructura de Base de Datos
El endpoint asume las siguientes tablas:
- `pedidos`: tabla principal de pedidos
- `pedidos_productos`: relación pedidos-productos
- `usuarios`: información de usuarios
- `sucursales`: información de sucursales
- `productos`: catálogo de productos

## Uso

### Para el Usuario Final
1. Aplicar filtros deseados (opcional)
2. Hacer clic en "Descargar Excel"
3. El archivo se descarga automáticamente
4. El nombre del archivo incluye la fecha: `pedidos_YYYY-MM-DD.xlsx`

### Para Desarrolladores
```javascript
// Llamar la función de descarga programáticamente
sistemaPedidos.descargarExcel();
```

## Personalización

### Modificar Columnas del Excel
Editar el array `$headers` en `exportar_pedidos_excel.php`:

```php
$headers = [
    'A1' => 'Folio',
    'B1' => 'Estado',
    // Agregar más columnas aquí
];
```

### Cambiar Estilos
Modificar las variables `$headerStyle` y `$dataStyle` en el mismo archivo.

### Agregar Más Filtros
1. Agregar campos en la interfaz (`Pedidos.php`)
2. Modificar la consulta SQL en `exportar_pedidos_excel.php`
3. Actualizar la función JavaScript `descargarExcel()`

## Consideraciones de Rendimiento

- **Límite de registros**: No hay límite implementado, pero se recomienda agregar paginación para grandes volúmenes
- **Memoria**: PhpSpreadsheet puede consumir mucha memoria con archivos grandes
- **Tiempo de ejecución**: Considerar aumentar `max_execution_time` para archivos grandes

## Seguridad

- **Autenticación**: Verificación de sesión de usuario
- **Validación**: Sanitización de parámetros de entrada
- **Autorización**: Solo usuarios autenticados pueden exportar

## Troubleshooting

### Error: "PhpSpreadsheet no encontrado"
- Verificar que Composer esté instalado
- Ejecutar `composer install` en el directorio raíz
- Verificar la ruta del autoloader

### Error: "No hay pedidos para exportar"
- Verificar que existan pedidos en la base de datos
- Ajustar los filtros aplicados
- Verificar la consulta SQL

### Archivo Excel corrupto
- Verificar permisos de escritura
- Verificar que PhpSpreadsheet esté correctamente instalado
- Revisar logs de error de PHP

## Futuras Mejoras

1. **Exportación programada**: Agregar exportaciones automáticas por email
2. **Múltiples formatos**: Agregar soporte para PDF y CSV
3. **Plantillas personalizables**: Permitir personalizar el formato del Excel
4. **Compresión**: Comprimir archivos grandes
5. **Progreso**: Mostrar barra de progreso para exportaciones grandes
