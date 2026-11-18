<?php

namespace App\Services;

use App\Models\PolicyRecommendation;
use App\Models\PovertyLevel;

class PolicyRecommendationService
{
    public static function defaultRecommendationsCatalog(): array
    {
        return [
            'muy_baja' => [
                [
                    'target_segment' => 'todos',
                    'dimension' => 'ingreso',
                    'title' => 'Inclusión Financiera Patrimonial y Fondos de Previsión',
                    'technical_action' => 'Consolidar esquemas de ahorro previsional, aseguramiento de vivienda y estímulos fiscales a la inversión en educación superior y transición energética.',
                    'ideal_framework_rationale' => 'Marco técnico ideal para preservar la estabilidad intergeneracional y evitar retrocesos ante crisis macroeconómicas.',
                    'priority' => 'preventiva',
                    'expected_impact' => 'Reducción de 1.5 a 3.0 puntos de riesgo ante choques externos.',
                ],
                [
                    'target_segment' => 'femenina',
                    'dimension' => 'genero',
                    'title' => 'Corresponsabilidad Laboral y Paridad Salarial en Puestos Directivos',
                    'technical_action' => 'Certificación obligatoria de igualdad salarial y licencias parentales simétricas para eliminar la penalización por maternidad.',
                    'ideal_framework_rationale' => 'Asegura que la autonomía económica femenina se mantenga equiparable a la masculina en hogares de bienestar consolidado.',
                    'priority' => 'media',
                    'expected_impact' => 'Cierre de brecha de ingreso intrahogar en hasta 85%.',
                ],
            ],
            'baja' => [
                [
                    'target_segment' => 'todos',
                    'dimension' => 'salud',
                    'title' => 'Universalización de Seguridad Social y Seguro de Desempleo Temporal',
                    'technical_action' => 'Incorporación subsidiada al régimen de seguridad social formal y fondo de estabilización de ingresos por hasta 6 meses ante pérdida de empleo.',
                    'ideal_framework_rationale' => 'Actúa como amortiguador técnico para impedir que hogares con vulnerabilidad leve caigan en pobreza moderada por gastos médicos de bolsillo.',
                    'priority' => 'media',
                    'expected_impact' => 'Reducción estimada de 5 a 8 puntos en el Índice de Pobreza.',
                ],
                [
                    'target_segment' => 'femenina',
                    'dimension' => 'genero',
                    'title' => 'Sistema Público de Cuidados y Guarderías de Horario Extendido',
                    'technical_action' => 'Acceso gratuito a centros de cuidado infantil y de personas dependientes para liberar horas de trabajo remunerado femenino.',
                    'ideal_framework_rationale' => 'Disminuye la pobreza de tiempo femenina y facilita la inserción en empleos formales de jornada completa.',
                    'priority' => 'alta',
                    'expected_impact' => 'Incremento de 22% en el ingreso autónomo femenino.',
                ],
            ],
            'moderada' => [
                [
                    'target_segment' => 'todos',
                    'dimension' => 'ingreso',
                    'title' => 'Subsidio Escalonado al Ingreso Familiar y Tarifas Sociales en Servicios',
                    'technical_action' => 'Transferencia monetaria condicionada a permanencia escolar y tarifa social progresiva en agua potable, energía eléctrica y transporte público.',
                    'ideal_framework_rationale' => 'Alivia la proporción ingreso/gasto cuando el hogar destina más del 45% de su presupuesto a alimentación y servicios básicos.',
                    'priority' => 'alta',
                    'expected_impact' => 'Disminución de 9 a 14 puntos en el Índice de Pobreza en 18 meses.',
                ],
                [
                    'target_segment' => 'todos',
                    'dimension' => 'vivienda',
                    'title' => 'Programa de Mejoramiento Habitacional y Ampliación contra Hacinamiento',
                    'technical_action' => 'Subsidio directo en materiales y asistencia técnica para sustitución de techos/muros endebles y construcción de cuarto adicional.',
                    'ideal_framework_rationale' => 'Ataca de raíz la privación física de la vivienda sin endeudar el flujo mensual del hogar.',
                    'priority' => 'alta',
                    'expected_impact' => 'Reducción de 35% en carencia habitacional.',
                ],
                [
                    'target_segment' => 'femenina',
                    'dimension' => 'genero',
                    'title' => 'Crédito Productivo Tasa Cero y Capacitación Técnica para Jefas de Familia',
                    'technical_action' => 'Financiamiento semilla con garantía estatal y estipendio de capacitación en oficios de alta rentabilidad local.',
                    'ideal_framework_rationale' => 'Corrige la vulnerabilidad estructural de hogares con jefatura femenina o brecha severa de ingresos por género.',
                    'priority' => 'alta',
                    'expected_impact' => 'Reducción de 10 puntos en el Índice de Pobreza Femenina.',
                ],
            ],
            'alta' => [
                [
                    'target_segment' => 'todos',
                    'dimension' => 'alimentacion',
                    'title' => 'Canasta Alimentaria Garantizada y Comedores Comunitarios Nutricionales',
                    'technical_action' => 'Dotación mensual en tarjeta alimentaria para productos frescos de canasta básica y acceso prioritario a red de abasto social.',
                    'ideal_framework_rationale' => 'Marco técnico indispensable cuando la relación ingreso/gasto impide cubrir requerimientos calóricos y proteicos mínimos.',
                    'priority' => 'critica',
                    'expected_impact' => 'Reducción inmediata de 12 a 18 puntos en vulnerabilidad alimentaria y económica.',
                ],
                [
                    'target_segment' => 'todos',
                    'dimension' => 'servicios',
                    'title' => 'Infraestructura Básica Domiciliaria (Agua Entubada, Saneamiento y Piso Firme)',
                    'technical_action' => 'Inversión pública directa para conexión a red hidráulica, biodigestores o drenaje sanitario y sustitución total de pisos de tierra.',
                    'ideal_framework_rationale' => 'Erradica focos de enfermedades gastrointestinales y respiratorias que perpetúan el gasto catastrófico en salud.',
                    'priority' => 'critica',
                    'expected_impact' => 'Reducción de 15 puntos en el Índice de Privación Multidimensional.',
                ],
                [
                    'target_segment' => 'femenina',
                    'dimension' => 'genero',
                    'title' => 'Renta Básica para Mujeres en Situación de Pobreza Severa y Red de Protección',
                    'technical_action' => 'Transferencia monetaria directa titularizada a las mujeres adultas del hogar junto con remuneración social al trabajo de cuidados.',
                    'ideal_framework_rationale' => 'Garantiza autonomía económica mínima y reduce la dependencia patrimonial en contextos de privación severa.',
                    'priority' => 'critica',
                    'expected_impact' => 'Reducción de 16 puntos en el Índice de Pobreza Femenina.',
                ],
            ],
            'muy_alta' => [
                [
                    'target_segment' => 'todos',
                    'dimension' => 'ingreso',
                    'title' => 'Ingreso Ciudadano de Emergencia Vital y Condonación de Pasivos Básicos',
                    'technical_action' => 'Transferencia monetaria incondicional equivalente a la línea de bienestar extremo per cápita y gratuidad total en agua y energía mínima vital.',
                    'ideal_framework_rationale' => 'Marco técnico de urgencia máxima: con un IP ≥ 80 el hogar enfrenta déficit absoluto de supervivencia que el mercado laboral no corrige por sí solo.',
                    'priority' => 'critica',
                    'expected_impact' => 'Reducción de 20 a 28 puntos en el Índice de Pobreza durante el primer año.',
                ],
                [
                    'target_segment' => 'todos',
                    'dimension' => 'vivienda',
                    'title' => 'Reconstrucción Integral de Vivienda Social y Atención Médica Domiciliaria',
                    'technical_action' => 'Sustitución integral de vivienda precaria por módulo habitacional seguro, brigadas médicas periódicas y becas de retención escolar al 100%.',
                    'ideal_framework_rationale' => 'Rompe la trampa de pobreza extrema multidimensional actuando simultáneamente en salud, hábitat y educación.',
                    'priority' => 'critica',
                    'expected_impact' => 'Transición de Pobreza Muy Alta hacia Pobreza Moderada en 24 a 36 meses.',
                ],
                [
                    'target_segment' => 'femenina',
                    'dimension' => 'genero',
                    'title' => 'Intervención Integral de Emergencia contra la Feminización de la Pobreza Extrema',
                    'technical_action' => 'Paquete prioritario de transferencia directa a mujeres, atención materna/infantil gratuita y comedores de cuidado comunitario.',
                    'ideal_framework_rationale' => 'Mitiga el impacto desproporcionado de la pobreza extrema sobre mujeres y niñas del hogar.',
                    'priority' => 'critica',
                    'expected_impact' => 'Disminución de hasta 24 puntos en el Índice de Pobreza Femenina.',
                ],
            ],
        ];
    }

    public function recommendForCalculation(
        string $levelSlug,
        array $dimensionBreakdown,
        float $femaleIndex,
        float $maleIndex
    ): array {
        $recommendations = [];

        try {
            $level = PovertyLevel::where('slug', $levelSlug)->first();
            if ($level) {
                $dbRecs = PolicyRecommendation::where('poverty_level_id', $level->id)
                    ->where('is_active', true)
                    ->get();
                foreach ($dbRecs as $rec) {
                    $recommendations[] = [
                        'title' => $rec->title,
                        'dimension' => $rec->dimension,
                        'target_segment' => $rec->target_segment,
                        'priority' => $rec->priority,
                        'technical_action' => $rec->technical_action,
                        'ideal_framework_rationale' => $rec->ideal_framework_rationale,
                        'expected_impact' => $rec->expected_impact,
                    ];
                }
            }
        } catch (\Throwable) {
            // Fallback to static catalog
        }

        if (empty($recommendations)) {
            $recommendations = self::defaultRecommendationsCatalog()[$levelSlug] ?? self::defaultRecommendationsCatalog()['moderada'];
        }

        // Añadir recomendación focalizada si la pobreza femenina supera a la masculina por más de 4 puntos
        if (($femaleIndex - $maleIndex) >= 4.0) {
            $recommendations[] = [
                'title' => 'Focalización por Brecha de Pobreza Femenina Intrahogar (+' . number_format($femaleIndex - $maleIndex, 1) . ' pts)',
                'dimension' => 'genero',
                'target_segment' => 'femenina',
                'priority' => 'alta',
                'technical_action' => 'Implementar transferencia directa a las mujeres del hogar y redistribución social del cuidado para compensar el déficit de ingreso autónomo femenino y la sobrecarga de horas no remuneradas.',
                'ideal_framework_rationale' => 'Diagnóstico de segmentación: las mujeres del hogar experimentan mayor intensidad de pobreza que el promedio masculino del mismo hogar.',
                'expected_impact' => 'Convergencia gradual entre el Índice Femenino y Masculino en 12 a 24 meses.',
            ];
        }

        return $recommendations;
    }
}
