-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema gestioncomercialsaas
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema gestioncomercialsaas
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `gestioncomercialsaas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `gestioncomercialsaas` ;

-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`empresas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`empresas` (
  `id_empresa` INT NOT NULL AUTO_INCREMENT,
  `razon_social` VARCHAR(150) NOT NULL,
  `nombre_fantasia` VARCHAR(150) NULL DEFAULT NULL,
  `identificacion_fiscal` VARCHAR(20) NOT NULL COMMENT 'CUIT / RUC / RFC / NIT',
  `direccion` TEXT NULL DEFAULT NULL,
  `telefono` VARCHAR(30) NULL DEFAULT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `creado_en` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_empresa`),
  UNIQUE INDEX `identificacion_fiscal` (`identificacion_fiscal` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`sucursales`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`sucursales` (
  `id_sucursal` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(10) NOT NULL,
  `direccion` TEXT NULL DEFAULT NULL,
  `telefono` VARCHAR(30) NULL DEFAULT NULL,
  `es_casa_matriz` TINYINT(1) NOT NULL DEFAULT '0',
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_sucursal`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE,
  INDEX `fk_sucursales_empresa` (`id_empresa` ASC) VISIBLE,
  CONSTRAINT `fk_sucursales_empresa`
    FOREIGN KEY (`id_empresa`)
    REFERENCES `gestioncomercialsaas`.`empresas` (`id_empresa`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`almacenes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`almacenes` (
  `id_almacen` INT NOT NULL AUTO_INCREMENT,
  `id_sucursal` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `ubicacion` TEXT NULL DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_almacen`),
  INDEX `fk_almacen_sucursal` (`id_sucursal` ASC) VISIBLE,
  CONSTRAINT `fk_almacen_sucursal`
    FOREIGN KEY (`id_sucursal`)
    REFERENCES `gestioncomercialsaas`.`sucursales` (`id_sucursal`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`cajas_chicas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`cajas_chicas` (
  `id_caja` INT NOT NULL AUTO_INCREMENT,
  `id_sucursal` INT NOT NULL,
  `nombre` VARCHAR(50) NOT NULL COMMENT 'Caja 1 POS, Caja Principal',
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_caja`),
  INDEX `fk_cajas_sucursal` (`id_sucursal` ASC) VISIBLE,
  CONSTRAINT `fk_cajas_sucursal`
    FOREIGN KEY (`id_sucursal`)
    REFERENCES `gestioncomercialsaas`.`sucursales` (`id_sucursal`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`aperturas_cierres_caja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`aperturas_cierres_caja` (
  `id_sesion_caja` INT NOT NULL AUTO_INCREMENT,
  `id_caja` INT NOT NULL,
  `id_usuario_apertura` INT NOT NULL,
  `id_usuario_cierre` INT NULL DEFAULT NULL,
  `fecha_apertura` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` DATETIME NULL DEFAULT NULL,
  `monto_inicial` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `monto_final_sistema` DECIMAL(12,2) NULL DEFAULT '0.00',
  `monto_final_real` DECIMAL(12,2) NULL DEFAULT '0.00' COMMENT 'Declarado en el arqueo',
  `diferencia` DECIMAL(12,2) NULL DEFAULT '0.00',
  `estado` ENUM('ABIERTA', 'CERRADA') NOT NULL DEFAULT 'ABIERTA',
  PRIMARY KEY (`id_sesion_caja`),
  INDEX `fk_sesion_caja` (`id_caja` ASC) VISIBLE,
  CONSTRAINT `fk_sesion_caja`
    FOREIGN KEY (`id_caja`)
    REFERENCES `gestioncomercialsaas`.`cajas_chicas` (`id_caja`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`cache`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  INDEX `cache_expiration_index` (`expiration` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`cache_locks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  INDEX `cache_locks_expiration_index` (`expiration` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`categorias` (
  `id_categoria` INT NOT NULL AUTO_INCREMENT,
  `id_categoria_padre` INT NULL DEFAULT NULL COMMENT 'Permite jerarquías y subcategorías',
  `nombre` VARCHAR(100) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_categoria`),
  INDEX `fk_categorias_padre` (`id_categoria_padre` ASC) VISIBLE,
  CONSTRAINT `fk_categorias_padre`
    FOREIGN KEY (`id_categoria_padre`)
    REFERENCES `gestioncomercialsaas`.`categorias` (`id_categoria`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`personas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`personas` (
  `id_persona` INT NOT NULL AUTO_INCREMENT,
  `tipo_persona` ENUM('CLIENTE', 'PROVEEDOR', 'EMPLEADO', 'AMBOS') NOT NULL,
  `razon_social_nombre` VARCHAR(150) NOT NULL,
  `tipo_documento` ENUM('DNI', 'CUIT', 'CUIL', 'RUC', 'PASAPORTE', 'OTRO') NOT NULL,
  `numero_documento` VARCHAR(25) NOT NULL,
  `condicion_iva` VARCHAR(50) NOT NULL DEFAULT 'Consumidor Final',
  `direccion` TEXT NULL DEFAULT NULL,
  `telefono` VARCHAR(30) NULL DEFAULT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `limite_credito` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Límite para ventas a crédito',
  `dias_credito` INT NOT NULL DEFAULT '0' COMMENT 'Plazo habitual de pago',
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  `creado_en` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_persona`),
  UNIQUE INDEX `numero_documento` (`numero_documento` ASC) VISIBLE,
  INDEX `idx_personas_doc` (`numero_documento` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`ventas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`ventas` (
  `id_venta` INT NOT NULL AUTO_INCREMENT,
  `id_sucursal` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `id_vendedor` INT NOT NULL,
  `id_sesion_caja` INT NULL DEFAULT NULL,
  `fecha_venta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_venta` ENUM('CONTADO', 'CREDITO') NOT NULL DEFAULT 'CONTADO',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `descuento_total` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `impuesto_total` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `total_venta` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `estado` ENUM('COMPLETADA', 'PRESUPUESTO', 'ANULADA') NOT NULL DEFAULT 'COMPLETADA',
  PRIMARY KEY (`id_venta`),
  INDEX `fk_ventas_sucursal` (`id_sucursal` ASC) VISIBLE,
  INDEX `fk_ventas_cliente` (`id_cliente` ASC) VISIBLE,
  INDEX `fk_ventas_sesion` (`id_sesion_caja` ASC) VISIBLE,
  INDEX `idx_ventas_fecha_cliente` (`fecha_venta` ASC, `id_cliente` ASC) VISIBLE,
  CONSTRAINT `fk_ventas_cliente`
    FOREIGN KEY (`id_cliente`)
    REFERENCES `gestioncomercialsaas`.`personas` (`id_persona`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_sesion`
    FOREIGN KEY (`id_sesion_caja`)
    REFERENCES `gestioncomercialsaas`.`aperturas_cierres_caja` (`id_sesion_caja`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_sucursal`
    FOREIGN KEY (`id_sucursal`)
    REFERENCES `gestioncomercialsaas`.`sucursales` (`id_sucursal`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`cuentas_por_cobrar`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`cuentas_por_cobrar` (
  `id_cxc` INT NOT NULL AUTO_INCREMENT,
  `id_venta` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `fecha_vencimiento` DATE NOT NULL,
  `monto_total` DECIMAL(12,2) NOT NULL,
  `monto_cobrado` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `saldo_pendiente` DECIMAL(12,2) GENERATED ALWAYS AS ((`monto_total` - `monto_cobrado`)) STORED,
  `estado` ENUM('PENDIENTE', 'PAGADO_PARCIAL', 'PAGADO') NOT NULL DEFAULT 'PENDIENTE',
  PRIMARY KEY (`id_cxc`),
  INDEX `fk_cxc_venta` (`id_venta` ASC) VISIBLE,
  INDEX `fk_cxc_cliente` (`id_cliente` ASC) VISIBLE,
  INDEX `idx_cxc_estado_venc` (`estado` ASC, `fecha_vencimiento` ASC) VISIBLE,
  CONSTRAINT `fk_cxc_cliente`
    FOREIGN KEY (`id_cliente`)
    REFERENCES `gestioncomercialsaas`.`personas` (`id_persona`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_cxc_venta`
    FOREIGN KEY (`id_venta`)
    REFERENCES `gestioncomercialsaas`.`ventas` (`id_venta`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`compras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`compras` (
  `id_compra` INT NOT NULL AUTO_INCREMENT,
  `id_proveedor` INT NOT NULL,
  `id_sucursal` INT NOT NULL,
  `tipo_comprobante` VARCHAR(20) NOT NULL COMMENT 'Factura Proveedor, Remito',
  `numero_comprobante` VARCHAR(50) NOT NULL,
  `fecha_emision` DATE NOT NULL,
  `fecha_recepcion` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `condicion_pago` ENUM('CONTADO', 'CREDITO') NOT NULL DEFAULT 'CONTADO',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `total_impuestos` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `total_compra` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `estado` ENUM('RECIBIDO', 'PENDIENTE', 'ANULADO') NOT NULL DEFAULT 'RECIBIDO',
  PRIMARY KEY (`id_compra`),
  INDEX `fk_compra_proveedor` (`id_proveedor` ASC) VISIBLE,
  INDEX `fk_compra_sucursal` (`id_sucursal` ASC) VISIBLE,
  INDEX `idx_compras_fecha_prov` (`fecha_emision` ASC, `id_proveedor` ASC) VISIBLE,
  CONSTRAINT `fk_compra_proveedor`
    FOREIGN KEY (`id_proveedor`)
    REFERENCES `gestioncomercialsaas`.`personas` (`id_persona`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_compra_sucursal`
    FOREIGN KEY (`id_sucursal`)
    REFERENCES `gestioncomercialsaas`.`sucursales` (`id_sucursal`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`cuentas_por_pagar`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`cuentas_por_pagar` (
  `id_cxp` INT NOT NULL AUTO_INCREMENT,
  `id_compra` INT NOT NULL,
  `id_proveedor` INT NOT NULL,
  `fecha_vencimiento` DATE NOT NULL,
  `monto_total` DECIMAL(12,2) NOT NULL,
  `monto_pagado` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `saldo_pendiente` DECIMAL(12,2) GENERATED ALWAYS AS ((`monto_total` - `monto_pagado`)) STORED,
  `estado` ENUM('PENDIENTE', 'PAGADO_PARCIAL', 'PAGADO') NOT NULL DEFAULT 'PENDIENTE',
  PRIMARY KEY (`id_cxp`),
  INDEX `fk_cxp_compra` (`id_compra` ASC) VISIBLE,
  INDEX `fk_cxp_proveedor` (`id_proveedor` ASC) VISIBLE,
  INDEX `idx_cxp_estado_venc` (`estado` ASC, `fecha_vencimiento` ASC) VISIBLE,
  CONSTRAINT `fk_cxp_compra`
    FOREIGN KEY (`id_compra`)
    REFERENCES `gestioncomercialsaas`.`compras` (`id_compra`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_cxp_proveedor`
    FOREIGN KEY (`id_proveedor`)
    REFERENCES `gestioncomercialsaas`.`personas` (`id_persona`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`monedas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`monedas` (
  `id_moneda` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(5) NOT NULL COMMENT 'USD, ARS, EUR, MXN',
  `nombre` VARCHAR(50) NOT NULL,
  `simbolo` VARCHAR(5) NOT NULL,
  `tipo_cambio` DECIMAL(12,4) NOT NULL DEFAULT '1.0000' COMMENT 'Respecto a la moneda base',
  `es_moneda_base` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_moneda`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`cobros_pagos_transacciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`cobros_pagos_transacciones` (
  `id_transaccion_pago` INT NOT NULL AUTO_INCREMENT,
  `tipo_operacion` ENUM('COBRO_CLIENTE', 'PAGO_PROVEEDOR') NOT NULL,
  `id_cxc` INT NULL DEFAULT NULL,
  `id_cxp` INT NULL DEFAULT NULL,
  `id_moneda` INT NOT NULL,
  `monto` DECIMAL(12,2) NOT NULL,
  `medio_pago` ENUM('EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO', 'TRANSFERENCIA', 'CHEQUE') NOT NULL,
  `referencia_bancaria` VARCHAR(100) NULL DEFAULT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_transaccion_pago`),
  INDEX `fk_pago_cxc` (`id_cxc` ASC) VISIBLE,
  INDEX `fk_pago_cxp` (`id_cxp` ASC) VISIBLE,
  INDEX `fk_pago_moneda` (`id_moneda` ASC) VISIBLE,
  CONSTRAINT `fk_pago_cxc`
    FOREIGN KEY (`id_cxc`)
    REFERENCES `gestioncomercialsaas`.`cuentas_por_cobrar` (`id_cxc`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_cxp`
    FOREIGN KEY (`id_cxp`)
    REFERENCES `gestioncomercialsaas`.`cuentas_por_pagar` (`id_cxp`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_moneda`
    FOREIGN KEY (`id_moneda`)
    REFERENCES `gestioncomercialsaas`.`monedas` (`id_moneda`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`impuestos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`impuestos` (
  `id_impuesto` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL COMMENT 'IVA General, IVA Reducido, Exento, IGV',
  `porcentaje` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `es_predeterminado` TINYINT(1) NOT NULL DEFAULT '0',
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_impuesto`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`marcas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`marcas` (
  `id_marca` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_marca`),
  UNIQUE INDEX `nombre` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`productos` (
  `id_producto` INT NOT NULL AUTO_INCREMENT,
  `sku` VARCHAR(50) NOT NULL COMMENT 'Código interno de producto',
  `codigo_barras` VARCHAR(50) NULL DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NULL DEFAULT NULL,
  `id_categoria` INT NOT NULL,
  `id_marca` INT NULL DEFAULT NULL,
  `id_impuesto` INT NOT NULL,
  `unidad_medida` VARCHAR(20) NOT NULL DEFAULT 'Unidad',
  `precio_costo` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `aplica_inventario` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 para servicios, 1 para productos físicos',
  `stock_minimo` INT NOT NULL DEFAULT '0',
  `stock_maximo` INT NULL DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  `creado_en` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  UNIQUE INDEX `sku` (`sku` ASC) VISIBLE,
  UNIQUE INDEX `codigo_barras` (`codigo_barras` ASC) VISIBLE,
  INDEX `fk_prod_categoria` (`id_categoria` ASC) VISIBLE,
  INDEX `fk_prod_marca` (`id_marca` ASC) VISIBLE,
  INDEX `fk_prod_impuesto` (`id_impuesto` ASC) VISIBLE,
  INDEX `idx_prod_sku_barcode` (`sku` ASC, `codigo_barras` ASC) VISIBLE,
  CONSTRAINT `fk_prod_categoria`
    FOREIGN KEY (`id_categoria`)
    REFERENCES `gestioncomercialsaas`.`categorias` (`id_categoria`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_impuesto`
    FOREIGN KEY (`id_impuesto`)
    REFERENCES `gestioncomercialsaas`.`impuestos` (`id_impuesto`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_marca`
    FOREIGN KEY (`id_marca`)
    REFERENCES `gestioncomercialsaas`.`marcas` (`id_marca`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`detalle_compras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`detalle_compras` (
  `id_detalle_compra` INT NOT NULL AUTO_INCREMENT,
  `id_compra` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` DECIMAL(12,3) NOT NULL,
  `costo_unitario` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(12,2) GENERATED ALWAYS AS ((`cantidad` * `costo_unitario`)) STORED,
  PRIMARY KEY (`id_detalle_compra`),
  INDEX `fk_detcompra_encabezado` (`id_compra` ASC) VISIBLE,
  INDEX `fk_detcompra_prod` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `fk_detcompra_encabezado`
    FOREIGN KEY (`id_compra`)
    REFERENCES `gestioncomercialsaas`.`compras` (`id_compra`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_detcompra_prod`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`notas_credito_compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`notas_credito_compra` (
  `id_nota_credito_compra` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_compra` INT NOT NULL,
  `numero_nota` VARCHAR(30) NOT NULL,
  `fecha` DATE NOT NULL,
  `motivo` VARCHAR(255) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `total` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `estado` ENUM('EMITIDA', 'ANULADA') NOT NULL DEFAULT 'EMITIDA',
  `id_usuario` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id_nota_credito_compra`),
  UNIQUE INDEX `notas_credito_compra_numero_nota_unique` (`numero_nota` ASC) VISIBLE,
  INDEX `notas_credito_compra_id_compra_foreign` (`id_compra` ASC) VISIBLE,
  CONSTRAINT `notas_credito_compra_id_compra_foreign`
    FOREIGN KEY (`id_compra`)
    REFERENCES `gestioncomercialsaas`.`compras` (`id_compra`)
    ON DELETE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`detalle_notas_credito_compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`detalle_notas_credito_compra` (
  `id_detalle_ncc` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_nota_credito_compra` INT UNSIGNED NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` DECIMAL(12,3) NOT NULL,
  `costo_unitario` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id_detalle_ncc`),
  INDEX `detalle_notas_credito_compra_id_nota_credito_compra_foreign` (`id_nota_credito_compra` ASC) VISIBLE,
  INDEX `detalle_notas_credito_compra_id_producto_foreign` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `detalle_notas_credito_compra_id_nota_credito_compra_foreign`
    FOREIGN KEY (`id_nota_credito_compra`)
    REFERENCES `gestioncomercialsaas`.`notas_credito_compra` (`id_nota_credito_compra`)
    ON DELETE CASCADE,
  CONSTRAINT `detalle_notas_credito_compra_id_producto_foreign`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`notas_credito_venta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`notas_credito_venta` (
  `id_nota_credito_venta` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_venta` INT NOT NULL,
  `numero_nota` VARCHAR(30) NOT NULL,
  `fecha` DATE NOT NULL,
  `motivo` VARCHAR(255) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `monto_impuesto` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `total` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `estado` ENUM('EMITIDA', 'ANULADA') NOT NULL DEFAULT 'EMITIDA',
  `id_usuario` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id_nota_credito_venta`),
  UNIQUE INDEX `notas_credito_venta_numero_nota_unique` (`numero_nota` ASC) VISIBLE,
  INDEX `notas_credito_venta_id_venta_foreign` (`id_venta` ASC) VISIBLE,
  CONSTRAINT `notas_credito_venta_id_venta_foreign`
    FOREIGN KEY (`id_venta`)
    REFERENCES `gestioncomercialsaas`.`ventas` (`id_venta`)
    ON DELETE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`detalle_notas_credito_venta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`detalle_notas_credito_venta` (
  `id_detalle_ncv` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_nota_credito_venta` INT UNSIGNED NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` DECIMAL(12,3) NOT NULL,
  `precio_unitario` DECIMAL(12,2) NOT NULL,
  `monto_impuesto` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id_detalle_ncv`),
  INDEX `detalle_notas_credito_venta_id_nota_credito_venta_foreign` (`id_nota_credito_venta` ASC) VISIBLE,
  INDEX `detalle_notas_credito_venta_id_producto_foreign` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `detalle_notas_credito_venta_id_nota_credito_venta_foreign`
    FOREIGN KEY (`id_nota_credito_venta`)
    REFERENCES `gestioncomercialsaas`.`notas_credito_venta` (`id_nota_credito_venta`)
    ON DELETE CASCADE,
  CONSTRAINT `detalle_notas_credito_venta_id_producto_foreign`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`transferencias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`transferencias` (
  `id_transferencia` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_almacen_origen` INT NOT NULL,
  `id_almacen_destino` INT NOT NULL,
  `numero_remito` VARCHAR(30) NOT NULL,
  `motivo` VARCHAR(255) NOT NULL,
  `estado` ENUM('ENVIADA', 'RECIBIDA', 'RECIBIDA_CON_DIFERENCIA') NOT NULL DEFAULT 'ENVIADA',
  `fecha_envio` DATETIME NOT NULL,
  `fecha_recepcion` DATETIME NULL DEFAULT NULL,
  `observaciones_recepcion` VARCHAR(255) NULL DEFAULT NULL,
  `id_usuario_envio` BIGINT UNSIGNED NOT NULL,
  `id_usuario_recepcion` BIGINT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id_transferencia`),
  UNIQUE INDEX `transferencias_numero_remito_unique` (`numero_remito` ASC) VISIBLE,
  INDEX `transferencias_id_almacen_origen_foreign` (`id_almacen_origen` ASC) VISIBLE,
  INDEX `transferencias_id_almacen_destino_foreign` (`id_almacen_destino` ASC) VISIBLE,
  CONSTRAINT `transferencias_id_almacen_destino_foreign`
    FOREIGN KEY (`id_almacen_destino`)
    REFERENCES `gestioncomercialsaas`.`almacenes` (`id_almacen`)
    ON DELETE RESTRICT,
  CONSTRAINT `transferencias_id_almacen_origen_foreign`
    FOREIGN KEY (`id_almacen_origen`)
    REFERENCES `gestioncomercialsaas`.`almacenes` (`id_almacen`)
    ON DELETE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`detalle_transferencias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`detalle_transferencias` (
  `id_detalle_transferencia` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transferencia` INT UNSIGNED NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad_enviada` DECIMAL(12,3) NOT NULL,
  `cantidad_recibida` DECIMAL(12,3) NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle_transferencia`),
  INDEX `detalle_transferencias_id_transferencia_foreign` (`id_transferencia` ASC) VISIBLE,
  INDEX `detalle_transferencias_id_producto_foreign` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `detalle_transferencias_id_producto_foreign`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE RESTRICT,
  CONSTRAINT `detalle_transferencias_id_transferencia_foreign`
    FOREIGN KEY (`id_transferencia`)
    REFERENCES `gestioncomercialsaas`.`transferencias` (`id_transferencia`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`detalle_ventas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`detalle_ventas` (
  `id_detalle_venta` INT NOT NULL AUTO_INCREMENT,
  `id_venta` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` DECIMAL(12,3) NOT NULL,
  `precio_unitario` DECIMAL(12,2) NOT NULL,
  `descuento` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `monto_impuesto` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` DECIMAL(12,2) NOT NULL COMMENT 'Calculado: ((precio * cantidad) - desc) + impuesto',
  PRIMARY KEY (`id_detalle_venta`),
  INDEX `fk_detventas_venta` (`id_venta` ASC) VISIBLE,
  INDEX `fk_detventas_prod` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `fk_detventas_prod`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_detventas_venta`
    FOREIGN KEY (`id_venta`)
    REFERENCES `gestioncomercialsaas`.`ventas` (`id_venta`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`tipos_comprobante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`tipos_comprobante` (
  `id_tipo_comprobante` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(10) NOT NULL COMMENT 'FACT_A, FACT_B, TICKET, NOTA_CREDITO',
  `descripcion` VARCHAR(50) NOT NULL,
  `serie_prefijo` VARCHAR(10) NOT NULL,
  `correlativo_actual` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tipo_comprobante`),
  UNIQUE INDEX `codigo` (`codigo` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`facturas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`facturas` (
  `id_factura` INT NOT NULL AUTO_INCREMENT,
  `id_venta` INT NOT NULL,
  `id_tipo_comprobante` INT NOT NULL,
  `numero_factura` VARCHAR(30) NOT NULL COMMENT 'Ej: F001-00004521',
  `fecha_emision` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cae_fiscal` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Código fiscal legal de facturación electrónica',
  `vencimiento_cae` DATE NULL DEFAULT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `monto_iva` DECIMAL(12,2) NOT NULL,
  `total_facturado` DECIMAL(12,2) NOT NULL,
  `estado_fiscal` ENUM('EMITIDA', 'ANULADA', 'RECHAZADA') NOT NULL DEFAULT 'EMITIDA',
  PRIMARY KEY (`id_factura`),
  UNIQUE INDEX `id_venta` (`id_venta` ASC) VISIBLE,
  UNIQUE INDEX `numero_factura` (`numero_factura` ASC) VISIBLE,
  INDEX `fk_facturas_tipocomp` (`id_tipo_comprobante` ASC) VISIBLE,
  CONSTRAINT `fk_facturas_tipocomp`
    FOREIGN KEY (`id_tipo_comprobante`)
    REFERENCES `gestioncomercialsaas`.`tipos_comprobante` (`id_tipo_comprobante`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_facturas_venta`
    FOREIGN KEY (`id_venta`)
    REFERENCES `gestioncomercialsaas`.`ventas` (`id_venta`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`failed_jobs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` VARCHAR(255) NOT NULL,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `failed_jobs_uuid_unique` (`uuid` ASC) VISIBLE,
  INDEX `failed_jobs_connection_queue_failed_at_index` (`connection` ASC, `queue` ASC, `failed_at` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`job_batches`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL DEFAULT NULL,
  `cancelled_at` INT NULL DEFAULT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`jobs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` SMALLINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_queue_index` (`queue` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`listas_precios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`listas_precios` (
  `id_lista_precio` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL COMMENT 'Minorista, Mayorista, Distribuidor',
  `porcentaje_ganancia_base` DECIMAL(5,2) NULL DEFAULT '0.00',
  `es_predeterminada` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_lista_precio`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`logs_auditoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`logs_auditoria` (
  `id_log` BIGINT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `accion` VARCHAR(50) NOT NULL COMMENT 'INSERT, UPDATE, DELETE',
  `tabla_afectada` VARCHAR(50) NOT NULL,
  `id_registro_afectado` INT NOT NULL,
  `detalle_cambio` JSON NULL DEFAULT NULL COMMENT 'Datos anteriores y nuevos en JSON',
  `ip_origen` VARCHAR(45) NULL DEFAULT NULL,
  `fecha` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`migrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`permissions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`permissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `permissions_name_guard_name_unique` (`name` ASC, `guard_name` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`model_has_permissions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`),
  INDEX `model_has_permissions_model_id_model_type_index` (`model_id` ASC, `model_type` ASC) VISIBLE,
  CONSTRAINT `model_has_permissions_permission_id_foreign`
    FOREIGN KEY (`permission_id`)
    REFERENCES `gestioncomercialsaas`.`permissions` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `roles_name_guard_name_unique` (`name` ASC, `guard_name` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`model_has_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`model_has_roles` (
  `role_id` BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`),
  INDEX `model_has_roles_model_id_model_type_index` (`model_id` ASC, `model_type` ASC) VISIBLE,
  CONSTRAINT `model_has_roles_role_id_foreign`
    FOREIGN KEY (`role_id`)
    REFERENCES `gestioncomercialsaas`.`roles` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`movimientos_caja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`movimientos_caja` (
  `id_mov_caja` INT NOT NULL AUTO_INCREMENT,
  `id_sesion_caja` INT NOT NULL,
  `tipo` ENUM('INGRESO', 'EGRESO') NOT NULL,
  `concepto` VARCHAR(150) NOT NULL COMMENT 'Pago de servicio, retiro de efectivo, cobro venta',
  `monto` DECIMAL(12,2) NOT NULL,
  `id_forma_pago` INT NOT NULL,
  `fecha` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mov_caja`),
  INDEX `fk_movcaja_sesion` (`id_sesion_caja` ASC) VISIBLE,
  CONSTRAINT `fk_movcaja_sesion`
    FOREIGN KEY (`id_sesion_caja`)
    REFERENCES `gestioncomercialsaas`.`aperturas_cierres_caja` (`id_sesion_caja`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`movimientos_inventario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`movimientos_inventario` (
  `id_movimiento` INT NOT NULL AUTO_INCREMENT,
  `id_producto` INT NOT NULL,
  `id_almacen_origen` INT NULL DEFAULT NULL,
  `id_almacen_destino` INT NULL DEFAULT NULL,
  `tipo_movimiento` ENUM('ENTRADA_COMPRA', 'SALIDA_VENTA', 'AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO', 'TRANSFERENCIA', 'DEVOLUCION_VENTA', 'DEVOLUCION_COMPRA') NOT NULL,
  `cantidad` DECIMAL(12,3) NOT NULL,
  `costo_unitario` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `referencia_documento` VARCHAR(50) NULL DEFAULT NULL COMMENT 'N° Factura o Venta asociada',
  `motivo` TEXT NULL DEFAULT NULL,
  `id_usuario` INT NOT NULL,
  `fecha` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  INDEX `fk_mov_alm_orig` (`id_almacen_origen` ASC) VISIBLE,
  INDEX `fk_mov_alm_dest` (`id_almacen_destino` ASC) VISIBLE,
  INDEX `idx_mov_inv_prod_fecha` (`id_producto` ASC, `fecha` ASC) VISIBLE,
  CONSTRAINT `fk_mov_alm_dest`
    FOREIGN KEY (`id_almacen_destino`)
    REFERENCES `gestioncomercialsaas`.`almacenes` (`id_almacen`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_alm_orig`
    FOREIGN KEY (`id_almacen_origen`)
    REFERENCES `gestioncomercialsaas`.`almacenes` (`id_almacen`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_prod`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`password_reset_tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`precios_productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`precios_productos` (
  `id_precio_producto` INT NOT NULL AUTO_INCREMENT,
  `id_lista_precio` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `precio_venta` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id_precio_producto`),
  UNIQUE INDEX `uk_lista_producto` (`id_lista_precio` ASC, `id_producto` ASC) VISIBLE,
  INDEX `fk_precio_producto` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `fk_precio_lista`
    FOREIGN KEY (`id_lista_precio`)
    REFERENCES `gestioncomercialsaas`.`listas_precios` (`id_lista_precio`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_precio_producto`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`role_has_permissions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`role_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`),
  INDEX `role_has_permissions_role_id_foreign` (`role_id` ASC) VISIBLE,
  CONSTRAINT `role_has_permissions_permission_id_foreign`
    FOREIGN KEY (`permission_id`)
    REFERENCES `gestioncomercialsaas`.`permissions` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign`
    FOREIGN KEY (`role_id`)
    REFERENCES `gestioncomercialsaas`.`roles` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `sessions_user_id_index` (`user_id` ASC) VISIBLE,
  INDEX `sessions_last_activity_index` (`last_activity` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`stock`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`stock` (
  `id_stock` INT NOT NULL AUTO_INCREMENT,
  `id_producto` INT NOT NULL,
  `id_almacen` INT NOT NULL,
  `cantidad` DECIMAL(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Soporta decimales para Kg/Litros',
  `actualizado_en` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stock`),
  UNIQUE INDEX `uk_prod_almacen` (`id_producto` ASC, `id_almacen` ASC) VISIBLE,
  INDEX `fk_stock_alm` (`id_almacen` ASC) VISIBLE,
  CONSTRAINT `fk_stock_alm`
    FOREIGN KEY (`id_almacen`)
    REFERENCES `gestioncomercialsaas`.`almacenes` (`id_almacen`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_prod`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestioncomercialsaas`.`productos` (`id_producto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `gestioncomercialsaas`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestioncomercialsaas`.`users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `users_email_unique` (`email` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
