# Especificación de Actualización del Sistema: SIPP (2026-11-23)

**Documento:** `actualizacion-especificacion-2026-11-23.md`  
**Fecha:** 2026-11-23  
**Fuentes comparadas:**
- Documento previo: `docs/especificacion.md`
- Documento actualizado: `docs/especificacion2.md`

---

## 1. Resumen Ejecutivo de la Actualización

La actualización contenida en `especificacion2.md` amplía notablemente el alcance del sistema en cuatro dimensiones críticas:
1. **Unidades de análisis geográficas y territoriales:** Ya no se limita a personas y hogares, sino que incorpora **viviendas, localidades, zonas y municipios**.
2. **Nuevas fuentes oficiales y formatos de datos:** Incorporación formal del **Censo General de Población y Vivienda (múltiples años)** junto a la **Encuesta Nacional de Ingresos y Gastos de los Hogares (ENIGH por año)**, integrando un selector de encuestas/censos predefinidos y soporte nativo de **archivos Excel (.xlsx/.xls)** para importación y descarga.
3. **Esquema flexible de estratificación (número de niveles elegible):** El cálculo progresivo no binario ahora debe permitir al usuario o investigador **elegir el número de niveles de clasificación** (por ejemplo: escala clásica de 3, 4, 5 o 6 niveles, incorporando explícitamente *pobreza media baja* y *pobreza media alta*).
4. **Variables multidimensionales ampliadas:** Incorporación formal de **bienes de consumo/activos del hogar**, **educación** y **servicios domiciliarios detallados** junto con la relación ingreso-gasto.

---

## 2. Matriz Comparativa de Cambios (`especificacion.md` vs. `especificacion2.md`)

| Sección / Módulo | Especificación Original (`especificacion.md`) | Especificación Actualizada (`especificacion2.md`) | Tipo de Cambio |
| :--- | :--- | :--- | :--- |
| **Objetivo General** | Medición de personas y grupos mediante variables socioeconómicas y encuesta ingreso-gasto. | Medición de **personas, viviendas, localidades, zonas, municipios y grupos**; integrando ENIGH multi-anual, **Censo General de Población y Vivienda (varios años)** y datos emanados de **archivos Excel**. | **Ampliación Mayor**: Escala territorial y fuentes censales/Excel. |
| **Unidades de Observación** | Individuos y hogares. | Personas, viviendas, localidades, zonas de referencia, municipios y grupos. | **Nueva Característica**: Nivel de agregación espacial/territorial. |
| **Fuentes y Catálogo Oficial** | Cuestionario integrado y carga de archivos genéricos de encuestas ingreso-gasto. | Selector de **todas las encuestas ENIGH por año** y **Censos de Población y Vivienda por año**, con opción de **descargar datos en formato Excel**. | **Nueva Característica**: Catálogo de datasets precargados y exportador Excel. |
| **Variables Multidimensionales** | Ingresos y condiciones físicas de la vivienda. | Ingresos, **educación**, **bienes/activos del hogar**, condiciones físicas y **servicios de la vivienda**. | **Ampliación**: Nuevas dimensiones de carencia (bienes y educación estructurada). |
| **Segmentación** | General, femenina y masculina dentro del hogar. | General, femenina y masculina dentro del hogar **o zona de referencia**. | **Ampliación**: Segmentación de género contextualizada por territorio. |
| **Clasificación del Índice** | 5 niveles progresivos fijos (ej. Muy baja a Muy alta). | Niveles progresivos con **alternativas para elegir el número de niveles** (incorporando *Pobreza Media Baja* y *Pobreza Media Alta*). | **Nueva Característica**: Parametrización dinámica del esquema de corte/estratificación. |
| **Análisis Temporal y Pronósticos** | Basado únicamente en el histórico persistido en base de datos. | Basado en el histórico persistido **o en datos ingresados ad-hoc para diversos años**. | **Ampliación**: Simulación de series temporales en memoria / ad-hoc. |
| **Usuarios Objetivo** | Individuos e investigadores/instituciones enfocados en series temporales. | Individuos e investigadores/instituciones enfocados en series temporales **y distintas zonas territoriales**. | **Ampliación**: Comparativa y benchmark territorial intermunicipal/regional. |

---

## 3. Especificación Detallada de Nuevas Características

### Característica 1: Ampliación de Sujetos de Estudio y Agregación Territorial
- **Descripción:** La aplicación debe modelar y clasificar no sólo a personas y hogares, sino entidades territoriales: **Viviendas, Localidades, Zonas y Municipios**.
- **Requisitos de Sistema:**
  - Capacidad de asignar claves geoestadísticas (INEGI / Entidad, Municipio, Localidad, AGEB/Zona).
  - Selector de nivel territorial en el cuestionario, importador y dashboard:
    - *Nivel 1:* Persona / Individuo
    - *Nivel 2:* Vivienda / Hogar
    - *Nivel 3:* Localidad / Comunidad
    - *Nivel 4:* Zona de referencia (Urbana / Periurbana / Rural / Polígono de atención prioritaria)
    - *Nivel 5:* Municipio / Demarcación territorial

### Característica 2: Catálogo Oficial de Encuestas ENIGH y Censos de Población y Vivienda
- **Descripción:** Presentar como opciones interactivas predeterminadas las diferentes ediciones oficiales de la ENIGH y del Censo de Población y Vivienda.
- **Requisitos de Sistema:**
  - **Selector de Fuentes Oficiales:**
    - Encuestas ENIGH disponibles por año (ej. 2016, 2018, 2020, 2022, 2024, 2026).
    - Censos Generales de Población y Vivienda por año (ej. 2010, 2020, Conteos intercensales 2015, 2025).
  - Carga con un solo clic de muestras y agregados representativos de cada edición para análisis inmediato sin requerir carga manual.

### Característica 3: Soporte Completo para Archivos Excel (.xlsx / .xls)
- **Descripción:** Incorporar importación masiva y descarga de datos en formato Excel nativo.
- **Requisitos de Sistema:**
  - **Importación:** Carga de hojas de cálculo Excel (`.xlsx`, `.xls`) con validación de columnas y cabeceras oficiales.
  - **Descarga:** Opción de exportar los datasets calculados, los lotes oficiales y las plantillas directamente en archivo Excel con formato profesional.

### Característica 4: Selector Dinámico del Número y Esquema de Niveles de Pobreza
- **Descripción:** El usuario e investigador deben poder elegir entre diferentes esquemas progresivos de clasificación para el Índice de Pobreza (IP 0–100).
- **Esquemas a soportar:**
  - **Esquema A (4 Niveles):**
    1. Pobreza Baja
    2. Pobreza Media Baja
    3. Pobreza Media Alta
    4. Pobreza Alta
  - **Esquema B (5 Niveles - Estándar Actual):**
    1. Muy Baja
    2. Baja
    3. Moderada
    4. Alta
    5. Muy Alta
  - **Esquema C (6 Niveles - Extendido según especificacion2.md):**
    1. Pobreza Muy Baja (Bienestar Pleno)
    2. Pobreza Baja (Vulnerabilidad Leve)
    3. Pobreza Media Baja
    4. Pobreza Media Alta
    5. Pobreza Alta (Severa)
    6. Pobreza Muy Alta (Extrema Crítica)
- La interfaz y el velocímetro visual deben adaptarse dinámicamente según el esquema seleccionado.

### Característica 5: Nuevas Dimensiones Multidimensionales (Bienes y Educación Estructurada)
- **Descripción:** Extender el cuestionario y el motor de cálculo para contemplar formalmente:
  - **Dotación de Bienes y Equipamiento del Hogar:** refrigerador, lavadora, estufa de gas, computadora/tablet, conectividad a internet, vehículo/transporte.
  - **Educación Estructurada:** nivel de escolaridad promedio, asistencia escolar de menores y rezago educativo normativo.
  - **Servicios Domiciliarios Ampliados:** agua entubada diaria, drenaje conectado a red, energía eléctrica y disponibilidad de combustible para cocinar.

### Característica 6: Pronósticos Ad-Hoc y Comparativa Multiterritorial
- **Descripción:**
  - Permitir al usuario ingresar series de datos manuales para 2 o más años directamente en la interfaz (sin obligar a guardarlos previamente en la base de datos) para generar pronósticos proyectados al vuelo.
  - Generar comparativas entre distintas **zonas territoriales o municipios** (ej. Municipio A vs. Municipio B; Zona Urbana vs. Zona Rural).

---

## 4. Repercusiones en la Arquitectura de la Aplicación

Para aplicar esta especificación a la aplicación existente en Laravel + FilamentPHP se requerirá:
1. **Base de Datos / Migraciones:**
   - Ampliar `households` o renombrar/extender a entidades territoriales (`geographic_zones`, `municipalities`, `subject_level`: *individuo*, *vivienda*, *localidad*, *zona*, *municipio*).
   - Añadir campos de `assets_score` (bienes) y desglose educativo en `poverty_evaluations`.
   - Crear tabla o configuración de esquemas de estratificación (`stratification_schemes`).
2. **Servicios del Dominio:**
   - Actualizar `PovertyCalculatorService` para admitir el parámetro dinámico de número de niveles (4, 5 o 6 niveles) y ponderar la dimensión de bienes.
   - Actualizar `PovertyForecastService` para admitir proyecciones desde arrays ad-hoc en memoria y agrupaciones territoriales.
   - Crear `OfficialDatasetsService` para gestionar el catálogo precargado de encuestas ENIGH y censos.
   - Instalar soporte de Excel (`maatwebsite/excel` o biblioteca nativa OpenSpout) para importación/exportación de archivos `.xlsx`.
3. **Interfaz FilamentPHP y Frontend:**
   - Añadir selector de fuentes oficiales (ENIGH por año y Censos por año).
   - Añadir selector del número de niveles en la Calculadora y en la configuración del panel.
   - Agregar selector de descarga y carga de archivos Excel.
   - Implementar vista de comparación multi-territorial.

---

## 5. Próximos Pasos de Implementación
Una vez aprobada esta especificación de actualización, se procederá con la ejecución técnica en el código fuente de la aplicación, manteniendo compatibilidad con las pruebas unitarias y funcionales existentes.
