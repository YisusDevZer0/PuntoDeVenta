# Funcionalidad de Exportación a Excel - Sistema de Pedidos

## Descripción
Se ha implementado una funcionalidad completa para exportar pedidos a formato Excel (.xlsx) con dos modalidades:
1. **Exportación general**: Todo el listado de pedidos con filtros aplicables
2. **Exportación individual**: Un pedido específico con información detallada

## Características Implementadas

### 1. Botones de Descarga Excel

#### A) Botón General
- **Ubicación**: Barra de acciones principal del sistema de pedidos
- **Estilo**: Botón verde con gradiente y efectos hover
- **Icono**: FontAwesome file-excel
- **Tooltip**: "Descargar listado de pedidos en formato Excel"
- **Función**: Exporta todos los pedidos con filtros aplicados

#### B) Botón Individual
- **Ubicación**: En cada pedido individual (grupo de botones de acción)
- **Estilo**: Botón verde más pequeño con efectos hover
- **Icono**: FontAwesome file-excel
- **Tooltip**: "Descargar Excel"
- **Función**: Exporta únicamente el pedido seleccionado

### 2. Filtros Aplicables
La exportación respeta todos los filtros activos:
- **Estado del pedido**: pendiente, aprobado, rechazado, en_proceso, completado, cancelado
- **Rango de fechas**: fecha inicio y fecha fin
- **Búsqueda**: texto libre que busca en folio, observaciones y usuario

### 3. Contenido del Excel

#### A) Exportación General
- **Encabezados principales**: Folio, Estado, Fecha, Usuario, Sucursal, Total, Prioridad, Observaciones, Productos
- **Información adicional**: Filtros aplicados, fecha de generación, total de pedidos

#### B) Exportación Individual
- **Información del pedido**: Folio, estado, fecha, usuario, sucursal, total, prioridad, observaciones
- **Productos detallados**: Código, nombre, descripción, cantidad, precio unitario, subtotal
- **Historial de cambios**: Estados anteriores, fechas de cambio, usuarios, comentarios
- **Metadatos**: Fecha de generación, usuario que generó el archivo

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
- Endpoint para generar archivo Excel general
- Consulta SQL con filtros aplicables
- Generación de archivo usando PhpSpreadsheet
- Headers HTTP para descarga automática

### 4. `api/exportar_pedido_excel.php` (NUEVO)
- Endpoint para generar archivo Excel individual
- Consulta SQL específica para un pedido
- Información detallada del pedido y productos
- Historial de cambios incluido

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

#### Exportación General
1. Aplicar filtros deseados (opcional)
2. Hacer clic en "Descargar Excel" en la barra principal
3. El archivo se descarga automáticamente
4. El nombre del archivo incluye la fecha: `pedidos_YYYY-MM-DD.xlsx`

#### Exportación Individual
1. Localizar el pedido deseado en la lista
2. Hacer clic en el botón verde de Excel (📊) en ese pedido
3. El archivo se descarga automáticamente
4. El nombre del archivo incluye el folio: `pedido_FOLIO_YYYY-MM-DD.xlsx`

### Para Desarrolladores
```javascript
// Llamar la función de descarga general programáticamente
sistemaPedidos.descargarExcel();

// Llamar la función de descarga individual programáticamente
sistemaPedidos.descargarExcelPedido(pedidoId);
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
