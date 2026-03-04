CREATE TABLE `usuarios` (
  `idUsuario` int NOT NULL,
  `idactivacion` int DEFAULT NULL,
  `Nombre` varchar(255) CHARACTER SET utf8mb3 NOT NULL,
  `Telefono` varchar(50) CHARACTER SET utf8mb3 DEFAULT NULL,
  `Correo` varchar(50) CHARACTER SET utf8mb3 NOT NULL,
  `Usuario` varchar(50) CHARACTER SET utf8mb3 NOT NULL,
  `Password` varchar(50) CHARACTER SET utf8mb3 NOT NULL,
  `Tipo` varchar(50) CHARACTER SET utf8mb3 NOT NULL,
  `Activo` varchar(50) CHARACTER SET utf8mb3 NOT NULL,
  `Imagen` varchar(100) CHARACTER SET utf8mb3 DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD KEY `idUsuario` (`idUsuario`);

ALTER TABLE `usuarios`
  MODIFY `idUsuario` int NOT NULL AUTO_INCREMENT;

CREATE TABLE `pendientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `area_origen` varchar(255) NOT NULL,
  `area_destino` varchar(255) NOT NULL,
  `usuario_responsable_id` int NOT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `estatus` enum('pendiente','seguimiento','pausa','programado','finalizado') NOT NULL DEFAULT 'pendiente',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_programada` date DEFAULT NULL,
  `fecha_finalizacion` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pendientes_usuario_responsable_id_foreign` (`usuario_responsable_id`),
  CONSTRAINT `pendientes_usuario_responsable_id_foreign` FOREIGN KEY (`usuario_responsable_id`) REFERENCES `usuarios` (`idUsuario`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pendiente_seguimientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pendiente_id` bigint unsigned NOT NULL,
  `usuario_id` int NOT NULL,
  `estatus_anterior` enum('pendiente','seguimiento','pausa','programado','finalizado') DEFAULT NULL,
  `estatus_nuevo` enum('pendiente','seguimiento','pausa','programado','finalizado') NOT NULL,
  `comentario` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pendiente_seguimientos_pendiente_id_foreign` (`pendiente_id`),
  KEY `pendiente_seguimientos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `pendiente_seguimientos_pendiente_id_foreign` FOREIGN KEY (`pendiente_id`) REFERENCES `pendientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pendiente_seguimientos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuario`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pendiente_adjuntos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pendiente_id` bigint unsigned NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `tamano_bytes` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pendiente_adjuntos_pendiente_id_foreign` (`pendiente_id`),
  CONSTRAINT `pendiente_adjuntos_pendiente_id_foreign` FOREIGN KEY (`pendiente_id`) REFERENCES `pendientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
