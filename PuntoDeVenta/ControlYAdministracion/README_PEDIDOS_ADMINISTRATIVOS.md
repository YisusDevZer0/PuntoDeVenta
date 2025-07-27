# 📋 Módulo de Pedidos Administrativos

## 🎯 Descripción General

El módulo de **Pedidos Administrativos** es una solución completa para la gestión de pedidos internos y administrativos del sistema. Permite crear, gestionar y dar seguimiento a pedidos de productos con funcionalidades avanzadas como integración con encargos, gestión de stock bajo y persistencia de datos.

## 🏗️ Arquitectura del Sistema

### Estructura de Archivos

```
ControlYAdministracion/
├── PedidosAdministrativos.php          # Página principal del módulo
├── js/
│   └── pedidos-administrativos.js     # Lógica JavaScript principal
├── api/
│   ├── buscar_productos.php           # Búsqueda de productos
│   ├── productos_stock_bajo.php       # Productos con bajo stock
│   ├── encargos_disponibles.php       # Encargos disponibles
│   ├── guardar_pedido.php             # Guardar pedidos
│   ├── pedidos_administrativos.php    # Listar y filtrar pedidos
│   ├── estadisticas_pedidos.php       # Estadísticas generales
│   ├── detalles_pedido.php            # Detalles de pedido específico
│   └── cambiar_estado_pedido.php      # Cambiar estado de pedidos
└── database/
    └── pedidos_administrativos.sql    # Estructura de base de datos
```

## 🎨 Características Principales

### 1. Cards Informativas
- **Pedidos Generados**: Total de pedidos creados
- **En Espera**: Pedidos pendientes de aprobación
- **Completados**: Pedidos finalizados exitosamente
- **Total Hoy**: Suma de pedidos del día actual

### 2. Gestión de Productos
- **Búsqueda Avanzada**: Por nombre o código de producto
- **Autocompletado**: Resultados en tiempo real
- **Validación de Stock**: Verificación de disponibilidad
- **Persistencia**: Datos guardados en localStorage

### 3. Modal de Nuevo Pedido
- **Búsqueda de Productos**: Interfaz intuitiva
- **Carrito de Compras**: Gestión visual de productos
- **Cálculo Automático**: Totales actualizados en tiempo real
- **Drag & Drop**: Reordenamiento de productos

### 4. Modal de Stock Bajo
- **Listado Automático**: Productos con stock insuficiente
- **Paginación**: Carga eficiente de datos
- **Agregado Masivo**: Incluir todos los productos de una vez
- **Integración**: Se conecta con el carrito principal

### 5. Modal de Encargos
- **Productos Especiales**: Encargos sin código fijo
- **Información de Cliente**: Datos del solicitante
- **Precios Estimados**: Valores aproximados
- **Integración Completa**: Se agregan al pedido normal

### 6. Modal de Resumen
- **Revisión Final**: Lista completa de productos
- **Ajuste de Cantidades**: Modificación en tiempo real
- **Observaciones**: Notas adicionales del pedido
- **Niveles de Prioridad**: Baja, Media, Alta, Urgente

## 🔧 Funcionalidades Técnicas

### Persistencia de Datos
```javascript
// Guardar en localStorage
localStorage.setItem('productosSeleccionados', JSON.stringify(productos));

// Cargar desde localStorage
const datos = localStorage.getItem('productosSeleccionados');
```

### Gestión de Estados
- **Pendiente**: Pedido creado, esperando aprobación
- **Aprobado**: Pedido autorizado para procesamiento
- **Completado**: Pedido finalizado y entregado
- **Cancelado**: Pedido anulado

### Integración con Inventario
- **Verificación de Stock**: Control automático de disponibilidad
- **Actualización Automática**: Reducción de stock al completar
- **Alertas de Stock Bajo**: Notificaciones proactivas

## 📊 Base de Datos

### Tabla Principal: `Pedidos_Administrativos`
```sql
CREATE TABLE Pedidos_Administrativos (
  ID_Pedido int(11) NOT NULL AUTO_INCREMENT,
  Fecha_Creacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Fecha_Modificacion datetime NULL,
  Solicitante varchar(255) NOT NULL,
  Total decimal(10,2) NOT NULL DEFAULT 0.00,
  Observaciones text NULL,
  Prioridad enum('baja','media','alta','urgente') NOT NULL DEFAULT 'media',
  Estado enum('pendiente','aprobado','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  Sucursal varchar(50) NOT NULL,
  PRIMARY KEY (ID_Pedido)
);
```

### Tabla de Detalles: `Pedidos_Administrativos_Detalle`
```sql
CREATE TABLE Pedidos_Administrativos_Detalle (
  ID_Detalle int(11) NOT NULL AUTO_INCREMENT,
  ID_Pedido int(11) NOT NULL,
  ID_Producto int(11) NULL,
  Cantidad int(11) NOT NULL DEFAULT 1,
  Precio_Unitario decimal(10,2) NOT NULL DEFAULT 0.00,
  Subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
  Es_Encargo tinyint(1) NOT NULL DEFAULT 0,
  ID_Encargo int(11) NULL,
  PRIMARY KEY (ID_Detalle)
);
```

## 🚀 Instalación y Configuración

### 1. Ejecutar Script SQL
```bash
mysql -u usuario -p base_datos < database/pedidos_administrativos.sql
```

### 2. Verificar Permisos
- Asegurar que el usuario tiene permisos de administrador
- Verificar acceso a las tablas de productos y encargos

### 3. Configurar Menú
Agregar el enlace en el menú principal:
```php
<a href="PedidosAdministrativos" class="nav-item nav-link">
    <i class="fas fa-shopping-cart me-2"></i>Pedidos Administrativos
</a>
```

## 🎯 Flujo de Trabajo

### 1. Crear Nuevo Pedido
1. Hacer clic en "Nuevo Pedido"
2. Buscar productos por nombre o código
3. Agregar productos al carrito
4. Ajustar cantidades si es necesario
5. Revisar resumen final
6. Confirmar y guardar pedido

### 2. Gestionar Stock Bajo
1. Hacer clic en "Bajo Stock"
2. Revisar productos con stock insuficiente
3. Seleccionar productos necesarios
4. Agregar al pedido principal

### 3. Procesar Encargos
1. Hacer clic en "Encargos"
2. Revisar encargos disponibles
3. Seleccionar encargos relevantes
4. Agregar al pedido como productos especiales

### 4. Aprobar/Cancelar Pedidos
1. Ver detalles del pedido
2. Confirmar acción (aprobar/cancelar)
3. Sistema actualiza estado automáticamente
4. Si se completa, se actualiza inventario

## 🔒 Seguridad

### Validaciones Implementadas
- **Sesión de Usuario**: Verificación de autenticación
- **Permisos de Sucursal**: Acceso solo a datos propios
- **Validación de Datos**: Sanitización de entradas
- **Prepared Statements**: Prevención de SQL Injection

### Confirmaciones de Usuario
- **Aprobar Pedido**: Confirmación explícita requerida
- **Cancelar Pedido**: Advertencia de acción irreversible
- **Agregar Productos**: Validación de duplicados

## 📱 Responsive Design

### Características Móviles
- **Diseño Adaptativo**: Funciona en tablets y móviles
- **Touch Friendly**: Botones y controles optimizados
- **Scroll Optimizado**: Navegación fluida en dispositivos táctiles

## 🔄 Mantenimiento

### Tareas Periódicas
- **Limpieza de localStorage**: Eliminar datos obsoletos
- **Optimización de Base de Datos**: Mantener índices actualizados
- **Backup de Datos**: Respaldo regular de pedidos

### Monitoreo
- **Logs de Errores**: Seguimiento de problemas
- **Métricas de Uso**: Estadísticas de utilización
- **Rendimiento**: Tiempos de respuesta

## 🚀 Futuras Mejoras

### Funcionalidades Planificadas
- **Notificaciones Push**: Alertas en tiempo real
- **Reportes Avanzados**: Análisis detallado de pedidos
- **Integración con Proveedores**: Pedidos automáticos
- **API Externa**: Conectividad con sistemas externos
- **Múltiples Usuarios**: Gestión de permisos granular

### Optimizaciones Técnicas
- **Caché Inteligente**: Mejora de rendimiento
- **Lazy Loading**: Carga progresiva de datos
- **WebSockets**: Comunicación en tiempo real
- **PWA**: Funcionalidad offline

## 📞 Soporte

### Contacto
- **Desarrollador**: Equipo de Desarrollo
- **Documentación**: Este archivo README
- **Issues**: Sistema de tickets interno

### Recursos Adicionales
- **Manual de Usuario**: Guía paso a paso
- **Videos Tutoriales**: Demostraciones visuales
- **FAQ**: Preguntas frecuentes

---

**Versión**: 1.0.0  
**Última Actualización**: Diciembre 2024  
**Compatibilidad**: PHP 7.4+, MySQL 5.7+, jQuery 3.6+ 