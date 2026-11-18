# Plan de Implementación: Aplicación de Cálculo y Proyección de Pobreza (Laravel + FilamentPHP + Frontend)

**Fecha y hora:** 2026-11-17 21:57  
**Documento base:** `docs/especificacion.md`

## 1. Objetivo General
Desarrollar una aplicación en Laravel + FilamentPHP con frontend público/interactivo y control de usuarios por roles (`admin`, `investigador`, `individuo`) orientada a calcular el nivel de pobreza de personas y grupos a partir de variables socioeconómicas multidimensionales, analizar series temporales multi-anuales, generar pronósticos a futuro y emitir recomendaciones técnicas de política pública.

## 2. Módulos del Sistema

### 2.1 Módulo de Ingreso de Datos y Variables
- **Cuestionario Integrado**: Disponible tanto en el Frontend público/autenticado como en el panel de FilamentPHP.
- **Importación Masiva (ENIGH / Estudios Oficiales)**: Cargador de archivos CSV con plantilla oficial descargable y procesamiento por lotes (`ImportBatch`).
- **Variables Multidimensionales**:
  - Ingreso mensual total, ingreso femenino, ingreso masculino.
  - Gasto mensual total, gasto en alimentos, gasto en salud/educación/vivienda.
  - Condiciones físicas de la vivienda (material de pisos, techos, muros, hacinamiento, servicios básicos de agua/drenaje/electricidad).
  - Carencias sociales (rezago educativo, acceso a salud, seguridad social, seguridad alimentaria).
- **Segmentación de Género**: Medición diferenciada de pobreza general, pobreza femenina y pobreza masculina dentro del hogar.

### 2.2 Módulo de Cálculo y Clasificación (Índice de Pobreza tipo IMC)
- **Índice de Pobreza (IP 0–100)**: Motor `PovertyCalculatorService` con escala intuitiva tipo IMC.
- **5 Niveles Progresivos (No Binarios)**:
  1. `0.0 - 19.9`: Pobreza Muy Baja (Bienestar Consolidado)
  2. `20.0 - 39.9`: Pobreza Baja (Vulnerabilidad Leve)
  3. `40.0 - 59.9`: Pobreza Moderada
  4. `60.0 - 79.9`: Pobreza Alta (Severa)
  5. `80.0 - 100.0`: Pobreza Muy Alta (Extrema Crítica)
- Explicación detallada del significado e implicaciones socioeconómicas de cada nivel.

### 2.3 Módulo de Análisis Temporal y Pronósticos
- Evaluación dinámica multi-anual (ej. 2020–2026) por hogar, individuo o grupo.
- Cálculo de tendencia histórica (pendiente de cambio anual, mejora/empeoramiento) mediante `PovertyForecastService`.
- Proyección a futuro (pronóstico de los próximos 3 años) en escenario inercial vs. escenario con intervención de política pública.

### 2.4 Módulo de Recomendaciones y Política Pública
- Catálogo parametrizado y motor `PolicyRecommendationService` que emite recomendaciones técnicas ideales por nivel de pobreza, dimensión crítica y segmento de género.

### 2.5 Control de Usuarios y Perfiles
- Roles:
  - **Administrador (`admin`)**: Control total de usuarios, catálogos de niveles y políticas, importaciones masivas y evaluaciones.
  - **Investigador / Institución (`investigador`)**: Gestión de hogares, evaluaciones multi-anuales, importaciones masivas ENIGH y análisis comparativo.
  - **Individuo (`individuo`)**: Uso de calculadora personal/familiar, seguimiento histórico propio y consulta de recomendaciones.

