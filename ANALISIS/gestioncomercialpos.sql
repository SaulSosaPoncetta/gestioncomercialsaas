-- ============================================================================
-- ARQUITECTURA DE BASE DE DATOS COMPLETA PARA SISTEMA DE GESTIÓN COMERCIAL (ERP/POS)
-- Motor: MySQL 8.0+ | Engine: InnoDB | Encoding: utf8mb4_unicode_ci
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `gestioncomercialpos`
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `gestioncomercialpos`;

-- ----------------------------------------------------------------------------
-- MÓDULO 1: CONFIGURACIÓN GENERAL Y SUCURSALES
-- ----------------------------------------------------------------------------

CREATE TABLE `empresas` (
  `id_empresa` INT AUTO_INCREMENT PRIMARY KEY,
  `razon_social` VARCHAR(150) NOT NULL,
  `nombre_fantasia` VARCHAR(150),
  `identificacion_fiscal` VARCHAR(20) UNIQUE NOT NULL COMMENT 'CUIT / RUC / RFC / NIT',
  `direccion` TEXT,
  `telefono` VARCHAR(30),
  `email` VARCHAR(100),
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `sucursales` (
  `id_sucursal` INT AUTO_INCREMENT PRIMARY KEY,
  `id_empresa` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(10) UNIQUE NOT NULL,
  `direccion` TEXT,
  `telefono` VARCHAR(30),
  `es_casa_matriz` TINYINT(1) NOT NULL DEFAULT 0,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT `fk_sucursales_empresa` FOREIGN KEY (`id_empresa`) 
    REFERENCES `empresas` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `impuestos` (
  `id_impuesto` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL COMMENT 'IVA General, IVA Reducido, Exento, IGV',
  `porcentaje` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
  `es_predeterminado` TINYINT(1) NOT NULL DEFAULT 0,
  `estado` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE `monedas` (
  `id_moneda` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(5) UNIQUE NOT NULL COMMENT 'USD, ARS, EUR, MXN',
  `nombre` VARCHAR(50) NOT NULL,
  `simbolo` VARCHAR(5) NOT NULL,
  `tipo_cambio` DECIMAL(12, 4) NOT NULL DEFAULT 1.0000 COMMENT 'Respecto a la moneda base',
  `es_moneda_base` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 2: ENTIDADES (CLIENTES, PROVEEDORES, EMPLEADOS)
-- ----------------------------------------------------------------------------

CREATE TABLE `personas` (
  `id_persona` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo_persona` ENUM('CLIENTE', 'PROVEEDOR', 'EMPLEADO', 'AMBOS') NOT NULL,
  `razon_social_nombre` VARCHAR(150) NOT NULL,
  `tipo_documento` ENUM('DNI', 'CUIT', 'CUIL', 'RUC', 'PASAPORTE', 'OTRO') NOT NULL,
  `numero_documento` VARCHAR(25) UNIQUE NOT NULL,
  `condicion_iva` VARCHAR(50) NOT NULL DEFAULT 'Consumidor Final',
  `direccion` TEXT,
  `telefono` VARCHAR(30),
  `email` VARCHAR(100),
  `limite_credito` DECIMAL(12, 2) NOT NULL DEFAULT 0.00 COMMENT 'Límite para ventas a crédito',
  `dias_credito` INT NOT NULL DEFAULT 0 COMMENT 'Plazo habitual de pago',
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 3: CATALOGACIÓN Y LISTAS DE PRECIOS
-- ----------------------------------------------------------------------------

CREATE TABLE `categorias` (
  `id_categoria` INT AUTO_INCREMENT PRIMARY KEY,
  `id_categoria_padre` INT DEFAULT NULL COMMENT 'Permite jerarquías y subcategorías',
  `nombre` VARCHAR(100) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT `fk_categorias_padre` FOREIGN KEY (`id_categoria_padre`) 
    REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `marcas` (
  `id_marca` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `estado` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE `productos` (
  `id_producto` INT AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Código interno de producto',
  `codigo_barras` VARCHAR(50) UNIQUE DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT,
  `id_categoria` INT NOT NULL,
  `id_marca` INT DEFAULT NULL,
  `id_impuesto` INT NOT NULL,
  `unidad_medida` VARCHAR(20) NOT NULL DEFAULT 'Unidad',
  `precio_costo` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `aplica_inventario` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 para servicios, 1 para productos físicos',
  `stock_minimo` INT NOT NULL DEFAULT 0,
  `stock_maximo` INT DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_prod_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_marca` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuestos` (`id_impuesto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `listas_precios` (
  `id_lista_precio` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL COMMENT 'Minorista, Mayorista, Distribuidor',
  `porcentaje_ganancia_base` DECIMAL(5, 2) DEFAULT 0.00,
  `es_predeterminada` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE `precios_productos` (
  `id_precio_producto` INT AUTO_INCREMENT PRIMARY KEY,
  `id_lista_precio` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `precio_venta` DECIMAL(12, 2) NOT NULL,
  CONSTRAINT `fk_precio_lista` FOREIGN KEY (`id_lista_precio`) REFERENCES `listas_precios` (`id_lista_precio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_precio_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `uk_lista_producto` UNIQUE (`id_lista_precio`, `id_producto`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 4: INVENTARIO Y ALMACENES MULTISUCURSAL
-- ----------------------------------------------------------------------------

CREATE TABLE `almacenes` (
  `id_almacen` INT AUTO_INCREMENT PRIMARY KEY,
  `id_sucursal` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `ubicacion` TEXT,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT `fk_almacen_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `stock` (
  `id_stock` INT AUTO_INCREMENT PRIMARY KEY,
  `id_producto` INT NOT NULL,
  `id_almacen` INT NOT NULL,
  `cantidad` DECIMAL(12, 3) NOT NULL DEFAULT 0.000 COMMENT 'Soporta decimales para Kg/Litros',
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_stock_prod` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_alm` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id_almacen`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `uk_prod_almacen` UNIQUE (`id_producto`, `id_almacen`),
  CONSTRAINT `chk_stock_positivo` CHECK (`cantidad` >= 0)
) ENGINE=InnoDB;

CREATE TABLE `movimientos_inventario` (
  `id_movimiento` INT AUTO_INCREMENT PRIMARY KEY,
  `id_producto` INT NOT NULL,
  `id_almacen_origen` INT DEFAULT NULL,
  `id_almacen_destino` INT DEFAULT NULL,
  `tipo_movimiento` ENUM('ENTRADA_COMPRA', 'SALIDA_VENTA', 'AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO', 'TRANSFERENCIA') NOT NULL,
  `cantidad` DECIMAL(12, 3) NOT NULL,
  `costo_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `referencia_documento` VARCHAR(50) DEFAULT NULL COMMENT 'N° Factura o Venta asociada',
  `motivo` TEXT,
  `id_usuario` INT NOT NULL,
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_mov_prod` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_alm_orig` FOREIGN KEY (`id_almacen_origen`) REFERENCES `almacenes` (`id_almacen`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_alm_dest` FOREIGN KEY (`id_almacen_destino`) REFERENCES `almacenes` (`id_almacen`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 5: CAJAS Y TESORERÍA (GESTIÓN DE FLUIDEZ MONETARIA)
-- ----------------------------------------------------------------------------

CREATE TABLE `cajas_chicas` (
  `id_caja` INT AUTO_INCREMENT PRIMARY KEY,
  `id_sucursal` INT NOT NULL,
  `nombre` VARCHAR(50) NOT NULL COMMENT 'Caja 1 POS, Caja Principal',
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT `fk_cajas_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `aperturas_cierres_caja` (
  `id_sesion_caja` INT AUTO_INCREMENT PRIMARY KEY,
  `id_caja` INT NOT NULL,
  `id_usuario_apertura` INT NOT NULL,
  `id_usuario_cierre` INT DEFAULT NULL,
  `fecha_apertura` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` DATETIME DEFAULT NULL,
  `monto_inicial` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `monto_final_sistema` DECIMAL(12, 2) DEFAULT 0.00,
  `monto_final_real` DECIMAL(12, 2) DEFAULT 0.00 COMMENT 'Declarado en el arqueo',
  `diferencia` DECIMAL(12, 2) DEFAULT 0.00,
  `estado` ENUM('ABIERTA', 'CERRADA') NOT NULL DEFAULT 'ABIERTA',
  CONSTRAINT `fk_sesion_caja` FOREIGN KEY (`id_caja`) REFERENCES `cajas_chicas` (`id_caja`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `movimientos_caja` (
  `id_mov_caja` INT AUTO_INCREMENT PRIMARY KEY,
  `id_sesion_caja` INT NOT NULL,
  `tipo` ENUM('INGRESO', 'EGRESO') NOT NULL,
  `concepto` VARCHAR(150) NOT NULL COMMENT 'Pago de servicio, retiro de efectivo, cobro venta',
  `monto` DECIMAL(12, 2) NOT NULL,
  `id_forma_pago` INT NOT NULL,
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_movcaja_sesion` FOREIGN KEY (`id_sesion_caja`) REFERENCES `aperturas_cierres_caja` (`id_sesion_caja`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 6: COMPRAS Y CUENTAS POR PAGAR (PROVEEDORES)
-- ----------------------------------------------------------------------------

CREATE TABLE `compras` (
  `id_compra` INT AUTO_INCREMENT PRIMARY KEY,
  `id_proveedor` INT NOT NULL,
  `id_sucursal` INT NOT NULL,
  `tipo_comprobante` VARCHAR(20) NOT NULL COMMENT 'Factura Proveedor, Remito',
  `numero_comprobante` VARCHAR(50) NOT NULL,
  `fecha_emision` DATE NOT NULL,
  `fecha_recepcion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `condicion_pago` ENUM('CONTADO', 'CREDITO') NOT NULL DEFAULT 'CONTADO',
  `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `total_impuestos` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `total_compra` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `estado` ENUM('RECIBIDO', 'PENDIENTE', 'ANULADO') NOT NULL DEFAULT 'RECIBIDO',
  CONSTRAINT `fk_compra_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `personas` (`id_persona`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `detalle_compras` (
  `id_detalle_compra` INT AUTO_INCREMENT PRIMARY KEY,
  `id_compra` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` DECIMAL(12, 3) NOT NULL,
  `costo_unitario` DECIMAL(12, 2) NOT NULL,
  `subtotal` DECIMAL(12, 2) GENERATED ALWAYS AS (`cantidad` * `costo_unitario`) STORED,
  CONSTRAINT `fk_detcompra_encabezado` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detcompra_prod` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `cuentas_por_pagar` (
  `id_cxp` INT AUTO_INCREMENT PRIMARY KEY,
  `id_compra` INT NOT NULL,
  `id_proveedor` INT NOT NULL,
  `fecha_vencimiento` DATE NOT NULL,
  `monto_total` DECIMAL(12, 2) NOT NULL,
  `monto_pagado` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `saldo_pendiente` DECIMAL(12, 2) GENERATED ALWAYS AS (`monto_total` - `monto_pagado`) STORED,
  `estado` ENUM('PENDIENTE', 'PAGADO_PARCIAL', 'PAGADO') NOT NULL DEFAULT 'PENDIENTE',
  CONSTRAINT `fk_cxp_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cxp_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `personas` (`id_persona`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 7: VENTAS Y PUNTOS DE VENTA (POS)
-- ----------------------------------------------------------------------------

CREATE TABLE `ventas` (
  `id_venta` INT AUTO_INCREMENT PRIMARY KEY,
  `id_sucursal` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `id_vendedor` INT NOT NULL,
  `id_sesion_caja` INT DEFAULT NULL,
  `fecha_venta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_venta` ENUM('CONTADO', 'CREDITO') NOT NULL DEFAULT 'CONTADO',
  `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `descuento_total` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `impuesto_total` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `total_venta` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `estado` ENUM('COMPLETADA', 'PRESUPUESTO', 'ANULADA') NOT NULL DEFAULT 'COMPLETADA',
  CONSTRAINT `fk_ventas_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `personas` (`id_persona`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_sesion` FOREIGN KEY (`id_sesion_caja`) REFERENCES `aperturas_cierres_caja` (`id_sesion_caja`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `detalle_ventas` (
  `id_detalle_venta` INT AUTO_INCREMENT PRIMARY KEY,
  `id_venta` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` DECIMAL(12, 3) NOT NULL,
  `precio_unitario` DECIMAL(12, 2) NOT NULL,
  `descuento` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `monto_impuesto` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `subtotal` DECIMAL(12, 2) NOT NULL COMMENT 'Calculado: ((precio * cantidad) - desc) + impuesto',
  CONSTRAINT `fk_detventas_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detventas_prod` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 8: FACTURACIÓN FISCAL Y CUENTAS POR COBRAR (CXC)
-- ----------------------------------------------------------------------------

CREATE TABLE `tipos_comprobante` (
  `id_tipo_comprobante` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(10) UNIQUE NOT NULL COMMENT 'FACT_A, FACT_B, TICKET, NOTA_CREDITO',
  `descripcion` VARCHAR(50) NOT NULL,
  `serie_prefijo` VARCHAR(10) NOT NULL,
  `correlativo_actual` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE `facturas` (
  `id_factura` INT AUTO_INCREMENT PRIMARY KEY,
  `id_venta` INT UNIQUE NOT NULL,
  `id_tipo_comprobante` INT NOT NULL,
  `numero_factura` VARCHAR(30) UNIQUE NOT NULL COMMENT 'Ej: F001-00004521',
  `fecha_emision` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cae_fiscal` VARCHAR(50) DEFAULT NULL COMMENT 'Código fiscal legal de facturación electrónica',
  `vencimiento_cae` DATE DEFAULT NULL,
  `subtotal` DECIMAL(12, 2) NOT NULL,
  `monto_iva` DECIMAL(12, 2) NOT NULL,
  `total_facturado` DECIMAL(12, 2) NOT NULL,
  `estado_fiscal` ENUM('EMITIDA', 'ANULADA', 'RECHAZADA') NOT NULL DEFAULT 'EMITIDA',
  CONSTRAINT `fk_facturas_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_facturas_tipocomp` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipos_comprobante` (`id_tipo_comprobante`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `cuentas_por_cobrar` (
  `id_cxc` INT AUTO_INCREMENT PRIMARY KEY,
  `id_venta` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `fecha_vencimiento` DATE NOT NULL,
  `monto_total` DECIMAL(12, 2) NOT NULL,
  `monto_cobrado` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `saldo_pendiente` DECIMAL(12, 2) GENERATED ALWAYS AS (`monto_total` - `monto_cobrado`) STORED,
  `estado` ENUM('PENDIENTE', 'PAGADO_PARCIAL', 'PAGADO') NOT NULL DEFAULT 'PENDIENTE',
  CONSTRAINT `fk_cxc_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cxc_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `personas` (`id_persona`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `cobros_pagos_transacciones` (
  `id_transaccion_pago` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo_operacion` ENUM('COBRO_CLIENTE', 'PAGO_PROVEEDOR') NOT NULL,
  `id_cxc` INT DEFAULT NULL,
  `id_cxp` INT DEFAULT NULL,
  `id_moneda` INT NOT NULL,
  `monto` DECIMAL(12, 2) NOT NULL,
  `medio_pago` ENUM('EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO', 'TRANSFERENCIA', 'CHEQUE') NOT NULL,
  `referencia_bancaria` VARCHAR(100) DEFAULT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pago_cxc` FOREIGN KEY (`id_cxc`) REFERENCES `cuentas_por_cobrar` (`id_cxc`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_cxp` FOREIGN KEY (`id_cxp`) REFERENCES `cuentas_por_pagar` (`id_cxp`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_moneda` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- MÓDULO 9: AUDITORÍA Y OPTIMIZACIÓN DE RENDIMIENTO
-- ----------------------------------------------------------------------------

CREATE TABLE `logs_auditoria` (
  `id_log` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `accion` VARCHAR(50) NOT NULL COMMENT 'INSERT, UPDATE, DELETE',
  `tabla_afectada` VARCHAR(50) NOT NULL,
  `id_registro_afectado` INT NOT NULL,
  `detalle_cambio` JSON DEFAULT NULL COMMENT 'Datos anteriores y nuevos en JSON',
  `ip_origen` VARCHAR(45) DEFAULT NULL,
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- ÍNDICES ESTRATÉGICOS DE CONSULTA RÁPIDA (INDEXING)
-- ----------------------------------------------------------------------------
CREATE INDEX `idx_prod_sku_barcode` ON `productos` (`sku`, `codigo_barras`);
CREATE INDEX `idx_personas_doc` ON `personas` (`numero_documento`);
CREATE INDEX `idx_ventas_fecha_cliente` ON `ventas` (`fecha_venta`, `id_cliente`);
CREATE INDEX `idx_compras_fecha_prov` ON `compras` (`fecha_emision`, `id_proveedor`);
CREATE INDEX `idx_mov_inv_prod_fecha` ON `movimientos_inventario` (`id_producto`, `fecha`);
CREATE INDEX `idx_cxc_estado_venc` ON `cuentas_por_cobrar` (`estado`, `fecha_vencimiento`);
CREATE INDEX `idx_cxp_estado_venc` ON `cuentas_por_pagar` (`estado`, `fecha_vencimiento`);