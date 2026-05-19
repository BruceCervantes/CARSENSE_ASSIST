<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>CarSense — Importar Datos</title>
<style>
body{font-family:monospace;background:#0d0d10;color:#ccc;padding:32px;max-width:800px}
h1{color:#e03030}h2{color:#f59e0b;margin-top:24px}
.ok{color:#10b981}.err{color:#e03030}.info{color:#3b82f6}
pre{background:#1a1a22;padding:12px;border-radius:8px;overflow-x:auto}
</style>
</head>
<body>
<h1>CarSense — Importar Datos</h1>
<?php
require_once __DIR__ . '/api/config/database.php';

function log_ok(string $msg): void { echo "<div class='ok'>✔ {$msg}</div>"; }
function log_info(string $msg): void { echo "<div class='info'>→ {$msg}</div>"; }
function log_err(string $msg): void { echo "<div class='err'>✘ {$msg}</div>"; }

$db = Database::getInstance();
log_ok('Conexión a base de datos establecida.');

// ── Limpiar tablas ─────────────────────────────
$db->exec('SET FOREIGN_KEY_CHECKS = 0');
$tables = [
    'diagnostic_sensations','diagnostic_when','diagnostic_zones','diagnostic_tags',
    'diagnostic_results','component_tips','component_symptoms','component_specs',
    'components','system_maintenance','system_symptoms','system_hotpoints','systems',
];
foreach ($tables as $t) { $db->exec("TRUNCATE TABLE `{$t}`"); }
$db->exec('SET FOREIGN_KEY_CHECKS = 1');
log_ok('Tablas limpiadas.');

// ════════════════════════════════════════════════
// SYSTEMS
// ════════════════════════════════════════════════
$sysStmt = $db->prepare(
    'INSERT INTO systems (slug,name,description,color,criticality,image_url,component_count,symptom_count)
     VALUES (:slug,:name,:description,:color,:criticality,:image_url,:component_count,:symptom_count)'
);

$systems = [
    ['frenos','Sistema de Frenos',
     'Responsable de desacelerar y detener el vehículo de forma segura. Funciona mediante la fricción entre pastillas y discos (o tambores). Es uno de los sistemas más críticos para la seguridad del conductor.',
     '#10b981','Alta',
     'https://images.unsplash.com/photo-1761040100208-07f603acc83f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
     6, 11],
    ['motor','Motor y Combustible',
     'Corazón del vehículo. Convierte el combustible en energía mecánica a través de ciclos de combustión controlada.',
     '#e03030','Alta',
     'https://images.unsplash.com/photo-1768929571671-4e58e2d9e72f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
     6, 12],
    ['suspension','Suspensión',
     'Amortigua los golpes del camino y mantiene el contacto de las ruedas con el pavimento, asegurando confort y estabilidad.',
     '#f59e0b','Media',
     'https://images.unsplash.com/photo-1760836395865-0c20fff2aefd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
     4, 9],
    ['electrico','Sistema Eléctrico',
     'Alimenta todos los sistemas electrónicos del vehículo: luces, arranque, sensores, control de motor y comodidades.',
     '#3b82f6','Media',
     'https://images.unsplash.com/photo-1737309469386-68fb1499a8c5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
     4, 14],
    ['enfriamiento','Sistema de Enfriamiento',
     'Regula la temperatura del motor para evitar el sobrecalentamiento. Circula refrigerante para disipar el calor generado en la combustión.',
     '#06b6d4','Alta',
     'https://images.unsplash.com/photo-1760804462141-442810513d4e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
     4, 8],
    ['escape','Sistema de Escape',
     'Expulsa los gases de combustión y reduce el ruido y las emisiones contaminantes del vehículo.',
     '#f97316','Media',
     'https://images.unsplash.com/photo-1760448970487-a3c6ff323b0a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
     4, 6],
];

foreach ($systems as [$slug,$name,$desc,$color,$crit,$img,$comps,$syms]) {
    $sysStmt->execute([':slug'=>$slug,':name'=>$name,':description'=>$desc,':color'=>$color,
                       ':criticality'=>$crit,':image_url'=>$img,':component_count'=>$comps,':symptom_count'=>$syms]);
}
log_ok('Sistemas insertados ('.count($systems).')');

// ════════════════════════════════════════════════
// SYSTEM HOTPOINTS
// ════════════════════════════════════════════════
$hpStmt = $db->prepare(
    'INSERT INTO system_hotpoints (system_slug,component_slug,name,x_pos,y_pos,sort_order) VALUES (?,?,?,?,?,?)'
);

$hotpoints = [
    ['frenos','pastillas-freno','Pastillas de Freno',22,60,1],
    ['frenos','disco-freno','Disco de Freno',40,45,2],
    ['frenos','pinzas-freno','Pinzas de Freno',32,28,3],
    ['frenos','liquido-frenos','Líquido de Frenos',72,30,4],
    ['frenos','cilindro-maestro','Cilindro Maestro',82,55,5],
    ['frenos','servofreno','Servofreno',88,70,6],
    ['motor','filtro-aire','Filtro de Aire',18,25,1],
    ['motor','bujias','Bujías',45,35,2],
    ['motor','correa-distribucion','Correa Distribución',30,60,3],
    ['motor','bomba-combustible','Bomba Combustible',70,75,4],
    ['motor','sensor-oxigeno','Sensor O2',80,45,5],
    ['motor','filtro-combustible','Filtro Combustible',55,70,6],
    ['suspension','amortiguadores','Amortiguadores',25,40,1],
    ['suspension','resortes','Resortes',65,35,2],
    ['suspension','rotulas','Rótulas',30,65,3],
    ['suspension','barra-estabilizadora','Barra Estabilizadora',50,75,4],
    ['electrico','bateria','Batería',20,35,1],
    ['electrico','alternador','Alternador',45,45,2],
    ['electrico','motor-arranque','Motor de Arranque',65,55,3],
    ['electrico','fusibles','Fusibles y Relés',78,30,4],
    ['enfriamiento','radiador','Radiador',15,40,1],
    ['enfriamiento','termostato','Termostato',45,35,2],
    ['enfriamiento','bomba-agua','Bomba de Agua',55,55,3],
    ['enfriamiento','refrigerante','Refrigerante',75,40,4],
    ['escape','catalizador','Catalizador',30,45,1],
    ['escape','silenciador','Silenciador',60,55,2],
    ['escape','sensor-lambda','Sensor Lambda',20,35,3],
    ['escape','tuberia-escape','Tubería',80,65,4],
];
foreach ($hotpoints as $hp) { $hpStmt->execute($hp); }
log_ok('Hotpoints insertados ('.count($hotpoints).')');

// ════════════════════════════════════════════════
// SYSTEM SYMPTOMS
// ════════════════════════════════════════════════
$ssStmt = $db->prepare(
    'INSERT INTO system_symptoms (system_slug,label,result_slug,sort_order) VALUES (?,?,?,?)'
);

$systemSymptoms = [
    ['frenos','Ruido al frenar','pastillas-freno-desgastadas',1],
    ['frenos','Vibración al frenar','disco-freno-irregular',2],
    ['frenos','Pedal esponjoso','pastillas-freno-desgastadas',3],
    ['frenos','Auto vira a un lado al frenar','pastillas-freno-desgastadas',4],
    ['frenos','Pedal va al piso','pastillas-freno-desgastadas',5],
    ['frenos','Luz de frenos encendida','pastillas-freno-desgastadas',6],
    ['motor','Pérdida de potencia','correa-distribucion-suelta',1],
    ['motor','Luz de check engine','bateria-carga-baja',2],
    ['motor','Ruido de motor','correa-distribucion-suelta',3],
    ['motor','Humo negro del escape','correa-distribucion-suelta',4],
    ['motor','Consumo excesivo de combustible','correa-distribucion-suelta',5],
    ['motor','Dificultad al arrancar','correa-distribucion-suelta',6],
    ['suspension','Vibración en el volante','disco-freno-irregular',1],
    ['suspension','Auto baila en curvas','disco-freno-irregular',2],
    ['suspension','Ruido de golpe en baches','disco-freno-irregular',3],
    ['suspension','Desgaste irregular de llantas','disco-freno-irregular',4],
    ['suspension','Auto jala a un lado','disco-freno-irregular',5],
    ['electrico','Batería descargada','bateria-carga-baja',1],
    ['electrico','Luces tenues','bateria-carga-baja',2],
    ['electrico','Carro no arranca','bateria-carga-baja',3],
    ['electrico','Luz de batería encendida','bateria-carga-baja',4],
    ['electrico','Alternador con ruido','bateria-carga-baja',5],
    ['enfriamiento','Motor recalentado','polvo-oxido-frenos',1],
    ['enfriamiento','Temperatura sube erráticamente','termostato-defectuoso',2],
    ['enfriamiento','Vapor bajo el cofre','polvo-oxido-frenos',3],
    ['enfriamiento','Pérdida de refrigerante','polvo-oxido-frenos',4],
    ['escape','Olor a quemado','bateria-carga-baja',1],
    ['escape','Humo excesivo','catalizador-obstruido',2],
    ['escape','Ruido fuerte de escape','bateria-carga-baja',3],
    ['escape','Pérdida de potencia','catalizador-obstruido',4],
];
foreach ($systemSymptoms as $ss) { $ssStmt->execute($ss); }
log_ok('Síntomas de sistema insertados ('.count($systemSymptoms).')');

// ════════════════════════════════════════════════
// SYSTEM MAINTENANCE
// ════════════════════════════════════════════════
$smStmt = $db->prepare(
    'INSERT INTO system_maintenance (system_slug,label,interval_text,sort_order) VALUES (?,?,?,?)'
);

$maintenance = [
    ['frenos','Pastillas de Freno','Revisar cada 20,000 km o 1 año. Reemplazar cuando el grosor baje de 3mm.',1],
    ['frenos','Líquido de Frenos','Cambiar cada 2 años o 40,000 km independientemente de su apariencia.',2],
    ['frenos','Discos de Freno','Inspeccionar en cada cambio de pastillas. Reemplazar si hay ranuras profundas.',3],
    ['motor','Cambio de aceite','Cada 5,000–10,000 km según tipo de aceite.',1],
    ['motor','Bujías','Cada 30,000–60,000 km según tipo.',2],
    ['motor','Filtro de aire','Cada 15,000–30,000 km o anualmente.',3],
    ['suspension','Amortiguadores','Revisar cada 50,000–80,000 km o si hay rebote excesivo.',1],
    ['suspension','Rótulas','Inspeccionar cada 60,000 km o ante ruidos y juego en ruedas.',2],
    ['electrico','Batería','Revisar cada 2 años. Reemplazar cada 3–5 años.',1],
    ['electrico','Alternador','Revisar ante indicios de descarga o luces fluctuantes.',2],
    ['enfriamiento','Refrigerante','Cambiar cada 2 años o 40,000 km.',1],
    ['enfriamiento','Termostato','Inspeccionar cada 60,000 km o ante variaciones de temperatura.',2],
    ['escape','Catalizador','Inspeccionar cada 80,000 km o ante pérdida de potencia.',1],
    ['escape','Sistema de escape','Revisar fugas cada 2 años o 40,000 km.',2],
];
foreach ($maintenance as $m) { $smStmt->execute($m); }
log_ok('Mantenimientos insertados ('.count($maintenance).')');

// ════════════════════════════════════════════════
// COMPONENTS
// ════════════════════════════════════════════════
$compStmt = $db->prepare(
    'INSERT INTO components (slug,system_slug,name,description,wear,wear_status,wear_label,image_url) VALUES (?,?,?,?,?,?,?,?)'
);

$components = [
    ['pastillas-freno','frenos','Pastillas de Freno','Material de fricción que presiona el disco para generar la fuerza de frenado.',82,'critical','82% de desgaste — Reemplazo urgente recomendado','https://images.unsplash.com/photo-1590488630628-df246379beab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['disco-freno','frenos','Disco de Freno','Pieza metálica circular que rota con la rueda y recibe la fricción de las pastillas.',55,'warning','55% — Revisar próximamente en siguiente mantenimiento','https://images.unsplash.com/photo-1761040100208-07f603acc83f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['pinzas-freno','frenos','Pinzas de Freno','Componente hidráulico que presiona las pastillas contra el disco al frenar.',25,'ok','25% — En buen estado','https://images.unsplash.com/photo-1590488630628-df246379beab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['liquido-frenos','frenos','Líquido de Frenos','Fluido hidráulico que transmite la presión del pedal hacia las pinzas.',60,'warning','60% — Cambio próximo recomendado','https://images.unsplash.com/photo-1590488630628-df246379beab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['cilindro-maestro','frenos','Cilindro Maestro','Convierte la fuerza mecánica del pedal en presión hidráulica.',20,'ok','20% — En buen estado','https://images.unsplash.com/photo-1590488630628-df246379beab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['servofreno','frenos','Servofreno (Booster)','Amplifica la fuerza aplicada sobre el pedal usando vacío del motor.',15,'ok','15% — En buen estado','https://images.unsplash.com/photo-1590488630628-df246379beab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['filtro-aire','motor','Filtro de Aire','Filtra el aire de admisión para proteger el motor de partículas de polvo y suciedad.',45,'warning','45% — Cambio próximo','https://images.unsplash.com/photo-1768929571671-4e58e2d9e72f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['bujias','motor','Bujías','Generan la chispa eléctrica que enciende la mezcla de aire y combustible.',50,'warning','50% — Revisar próximamente','https://images.unsplash.com/photo-1590488630628-df246379beab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['correa-distribucion','motor','Correa de Distribución','Sincroniza el movimiento del cigüeñal y los árboles de levas del motor.',70,'critical','70% — Atención próxima requerida','https://images.unsplash.com/photo-1573864698664-4a1fd0c97b46?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['bomba-combustible','motor','Bomba de Combustible','Suministra combustible desde el tanque hacia los inyectores del motor.',30,'ok','30% — En buen estado','https://images.unsplash.com/photo-1764538737417-3345e9f649e3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['sensor-oxigeno','motor','Sensor de Oxígeno','Mide el oxígeno residual en los gases de escape para optimizar la mezcla aire/combustible.',35,'ok','35% — En buen estado','https://images.unsplash.com/photo-1768929571671-4e58e2d9e72f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['filtro-combustible','motor','Filtro de Combustible','Retiene partículas e impurezas del combustible antes de llegar al motor.',55,'warning','55% — Cambio próximo recomendado','https://images.unsplash.com/photo-1764538737417-3345e9f649e3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['amortiguadores','suspension','Amortiguadores','Absorben la energía cinética de los baches y vibraciones del camino.',55,'warning','55% — Revisar próximamente','https://images.unsplash.com/photo-1769218403508-90c67335aab5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['resortes','suspension','Resortes','Soportan el peso del vehículo y absorben impactos transmitidos por el camino.',25,'ok','25% — En buen estado','https://images.unsplash.com/photo-1760836395865-0c20fff2aefd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['rotulas','suspension','Rótulas','Articulación esférica que permite el movimiento entre la suspensión y el portamangueta.',40,'ok','40% — En buen estado','https://images.unsplash.com/photo-1760836395865-0c20fff2aefd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['barra-estabilizadora','suspension','Barra Estabilizadora','Barra metálica que conecta ambas ruedas del mismo eje para reducir el balanceo lateral.',20,'ok','20% — En buen estado','https://images.unsplash.com/photo-1769218403508-90c67335aab5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['bateria','electrico','Batería','Almacena energía eléctrica para el arranque y alimenta los sistemas auxiliares.',60,'warning','60% — Vida útil media superada','https://images.unsplash.com/photo-1737309469386-68fb1499a8c5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['alternador','electrico','Alternador','Convierte la energía mecánica del motor en electricidad para cargar la batería.',30,'ok','30% — En buen estado','https://images.unsplash.com/photo-1674655798804-b739c31b6cf5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['motor-arranque','electrico','Motor de Arranque','Motor eléctrico que gira el motor de combustión para iniciarlo.',20,'ok','20% — En buen estado','https://images.unsplash.com/photo-1674655798804-b739c31b6cf5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['fusibles','electrico','Fusibles y Relés','Protegen los circuitos eléctricos de sobrecargas y cortocircuitos.',10,'ok','10% — En excelente estado','https://images.unsplash.com/photo-1737309469386-68fb1499a8c5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['radiador','enfriamiento','Radiador','Intercambiador de calor que disipa la temperatura del refrigerante al exterior.',35,'ok','35% — En buen estado','https://images.unsplash.com/photo-1760804462141-442810513d4e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['termostato','enfriamiento','Termostato','Válvula termostática que regula el flujo de refrigerante según la temperatura del motor.',45,'warning','45% — Revisar en próximo mantenimiento','https://images.unsplash.com/photo-1730461747568-7250e49eb50c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['bomba-agua','enfriamiento','Bomba de Agua','Impulsa el refrigerante a través de todo el sistema de enfriamiento.',40,'ok','40% — En buen estado','https://images.unsplash.com/photo-1760804462141-442810513d4e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['refrigerante','enfriamiento','Refrigerante','Fluido que circula por el sistema absorbiendo y disipando el calor del motor.',55,'warning','55% — Cambio próximo recomendado','https://images.unsplash.com/photo-1730461747568-7250e49eb50c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['catalizador','escape','Catalizador','Convierte los gases contaminantes del escape en emisiones menos dañinas.',40,'ok','40% — En buen estado','https://images.unsplash.com/photo-1759419281419-b04552b2691a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['silenciador','escape','Silenciador','Reduce el ruido producido por los gases de escape al salir del motor.',30,'ok','30% — En buen estado','https://images.unsplash.com/photo-1760448970487-a3c6ff323b0a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['sensor-lambda','escape','Sensor Lambda (O2)','Mide el oxígeno residual en los gases de escape para ajustar la mezcla.',35,'ok','35% — En buen estado','https://images.unsplash.com/photo-1759419281419-b04552b2691a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
    ['tuberia-escape','escape','Tubería de Escape','Conduce los gases de combustión desde el motor hasta la salida trasera.',25,'ok','25% — En buen estado','https://images.unsplash.com/photo-1760448970487-a3c6ff323b0a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80'],
];
foreach ($components as $c) { $compStmt->execute($c); }
log_ok('Componentes insertados ('.count($components).')');

// ════════════════════════════════════════════════
// COMPONENT SPECS
// ════════════════════════════════════════════════
$specStmt = $db->prepare('INSERT INTO component_specs (component_slug,spec_label,spec_value,sort_order) VALUES (?,?,?,?)');

$specs = [
    ['pastillas-freno','Material','Semimetálico / Orgánico / Cerámico',1],
    ['pastillas-freno','Grosor mínimo','3 mm',2],
    ['pastillas-freno','Grosor nuevo','10–12 mm',3],
    ['pastillas-freno','Vida útil','15,000–70,000 km',4],
    ['disco-freno','Material','Hierro fundido / Carbono-cerámica',1],
    ['disco-freno','Grosor mínimo','Varía por modelo',2],
    ['disco-freno','Vida útil','60,000–100,000 km',3],
    ['pinzas-freno','Tipo','Flotante / Fija',1],
    ['pinzas-freno','Pistones','1–6 según modelo',2],
    ['pinzas-freno','Vida útil','100,000–150,000 km',3],
    ['liquido-frenos','Especificación','DOT 3, DOT 4 o DOT 5.1',1],
    ['liquido-frenos','Punto de ebullición','205–270°C (seco)',2],
    ['liquido-frenos','Cambio recomendado','Cada 2 años o 40,000 km',3],
    ['cilindro-maestro','Tipo','Doble circuito (tándem)',1],
    ['cilindro-maestro','Vida útil','150,000–200,000 km',2],
    ['servofreno','Tipo','Vacío / Hidráulico',1],
    ['servofreno','Vida útil','150,000–200,000 km',2],
    ['filtro-aire','Tipo','Papel / Espuma / Cotton',1],
    ['filtro-aire','Cambio recomendado','Cada 15,000–30,000 km',2],
    ['bujias','Tipo','Cobre / Iridio / Platino',1],
    ['bujias','Gap','0.8–1.1 mm',2],
    ['bujias','Vida útil','30,000–100,000 km',3],
    ['correa-distribucion','Material','Caucho reforzado',1],
    ['correa-distribucion','Cambio','Cada 60,000–100,000 km',2],
    ['bomba-combustible','Tipo','Eléctrica (sumergida en tanque)',1],
    ['bomba-combustible','Presión','2.5–4.5 bar',2],
    ['bomba-combustible','Vida útil','100,000–150,000 km',3],
    ['sensor-oxigeno','Ubicación','Antes y después del catalizador',1],
    ['sensor-oxigeno','Vida útil','60,000–100,000 km',2],
    ['filtro-combustible','Cambio recomendado','Cada 30,000–60,000 km',1],
    ['amortiguadores','Tipo','Monotubo / Bitubo',1],
    ['amortiguadores','Vida útil','50,000–80,000 km',2],
    ['resortes','Tipo','Helicoidal / Ballesta',1],
    ['resortes','Vida útil','100,000–150,000 km',2],
    ['rotulas','Vida útil','60,000–120,000 km',1],
    ['barra-estabilizadora','Vida útil','100,000–200,000 km (bujes cada 40,000)',1],
    ['bateria','Voltaje','12V (convencional)',1],
    ['bateria','Capacidad','45–100 Ah según vehículo',2],
    ['bateria','Vida útil','3–5 años',3],
    ['alternador','Voltaje de carga','13.8–14.4 V',1],
    ['alternador','Vida útil','100,000–150,000 km',2],
    ['motor-arranque','Potencia','0.8–2.5 kW',1],
    ['motor-arranque','Vida útil','150,000–200,000 km',2],
    ['fusibles','Tipos','ATO, Mini, Micro, Midi, Maxi',1],
    ['fusibles','Amperaje','5–80A según circuito',2],
    ['radiador','Material','Aluminio / Cobre-latón',1],
    ['radiador','Vida útil','150,000–200,000 km',2],
    ['termostato','Temperatura de apertura','82–95°C',1],
    ['termostato','Vida útil','60,000–100,000 km',2],
    ['bomba-agua','Tipo','Centrífuga',1],
    ['bomba-agua','Vida útil','60,000–100,000 km',2],
    ['refrigerante','Concentración','50/50 (agua + anticongelante)',1],
    ['refrigerante','Cambio','Cada 2 años o 40,000 km',2],
    ['catalizador','Tipo','TWC (Three-Way Catalyst)',1],
    ['catalizador','Temperatura de operación','400–800°C',2],
    ['catalizador','Vida útil','80,000–160,000 km',3],
    ['silenciador','Material','Acero inoxidable / Al-Si',1],
    ['silenciador','Vida útil','80,000–150,000 km',2],
    ['sensor-lambda','Señal','0–1V (banda estrecha) / Variable (banda ancha)',1],
    ['sensor-lambda','Vida útil','60,000–100,000 km',2],
    ['tuberia-escape','Material','Acero al carbono / Inoxidable',1],
    ['tuberia-escape','Vida útil','100,000–200,000 km',2],
];
foreach ($specs as $s) { $specStmt->execute($s); }
log_ok('Especificaciones insertadas ('.count($specs).')');

// ════════════════════════════════════════════════
// COMPONENT SYMPTOMS
// ════════════════════════════════════════════════
$csStmt = $db->prepare('INSERT INTO component_symptoms (component_slug,symptom,sort_order) VALUES (?,?,?)');
$compSymptoms = [
    ['pastillas-freno','Chirrido metálico al frenar',1],['pastillas-freno','Vibración en el pedal',2],
    ['pastillas-freno','Pedal con más recorrido',3],['pastillas-freno','Olor a quemado tras frenar',4],
    ['disco-freno','Vibración al frenar',1],['disco-freno','Ranuras visibles en el disco',2],
    ['disco-freno','Ruido de raspado',3],['disco-freno','Disco con variación de grosor',4],
    ['pinzas-freno','Fuga de líquido de frenos',1],['pinzas-freno','Freno jalando a un lado',2],
    ['pinzas-freno','Pinza atascada (freno caliente)',3],['pinzas-freno','Pedal esponjoso',4],
    ['liquido-frenos','Pedal esponjoso o blando',1],['liquido-frenos','Frenos menos efectivos',2],
    ['liquido-frenos','Nivel bajo en depósito',3],['liquido-frenos','Líquido oscuro o turbio',4],
    ['cilindro-maestro','Pedal cae al fondo',1],['cilindro-maestro','Pérdida gradual de presión',2],
    ['cilindro-maestro','Fuga interna',3],['cilindro-maestro','Líquido bajo sin fugas visibles',4],
    ['servofreno','Pedal muy duro',1],['servofreno','Chirrido al frenar con motor apagado',2],
    ['servofreno','Mayor esfuerzo al pisar el freno',3],
    ['filtro-aire','Consumo elevado de combustible',1],['filtro-aire','Motor sin potencia',2],
    ['filtro-aire','Humo negro del escape',3],['filtro-aire','Ruido de succión inusual',4],
    ['bujias','Dificultad al arrancar',1],['bujias','Motor falla o tiembla',2],
    ['bujias','Consumo elevado',3],['bujias','Pérdida de potencia',4],
    ['correa-distribucion','Ruido de golpeteo al arrancar',1],['correa-distribucion','Motor no arranca',2],
    ['correa-distribucion','Pérdida de sincronía del motor',3],
    ['bomba-combustible','Motor no arranca',1],['bomba-combustible','Fallas de aceleración',2],
    ['bomba-combustible','Motor se apaga en marcha',3],['bomba-combustible','Ruido de zumbido desde el tanque',4],
    ['sensor-oxigeno','Check engine encendido',1],['sensor-oxigeno','Consumo elevado de combustible',2],
    ['sensor-oxigeno','Emisiones altas',3],['sensor-oxigeno','Pérdida de potencia',4],
    ['filtro-combustible','Motor falla bajo carga',1],['filtro-combustible','Arranque difícil',2],
    ['filtro-combustible','Pérdida de potencia progresiva',3],
    ['amortiguadores','Rebote excesivo',1],['amortiguadores','Vibración en el volante',2],
    ['amortiguadores','Auto baila en autopista',3],['amortiguadores','Desgaste irregular de llantas',4],
    ['resortes','Vehículo más bajo de lo normal',1],['resortes','Inclinación a un lado',2],
    ['resortes','Golpes al pasar topes',3],
    ['rotulas','Ruido de chasquido al girar',1],['rotulas','Juego en rueda al moverla',2],
    ['rotulas','Dirección imprecisa',3],['rotulas','Desgaste irregular de llantas',4],
    ['barra-estabilizadora','Balanceo excesivo en curvas',1],['barra-estabilizadora','Ruido de golpeteo en baches',2],
    ['barra-estabilizadora','Manejo impreciso',3],
    ['bateria','Motor gira lento al arrancar',1],['bateria','Luces tenues',2],
    ['bateria','Carro no arranca en frío',3],['bateria','Batería no retiene carga',4],
    ['alternador','Luz de batería encendida',1],['alternador','Batería se descarga rápido',2],
    ['alternador','Luces fluctúan con el acelerador',3],['alternador','Ruido de rodamiento',4],
    ['motor-arranque','Clic al girar la llave',1],['motor-arranque','Motor no responde',2],
    ['motor-arranque','Ruido de chirrido al arrancar',3],['motor-arranque','Arranque lento',4],
    ['fusibles','Sistema eléctrico que no funciona',1],['fusibles','Fusible fundido visible',2],
    ['fusibles','Componente sin energía',3],
    ['radiador','Sobrecalentamiento',1],['radiador','Fugas de refrigerante',2],
    ['radiador','Nivel de refrigerante bajo',3],['radiador','Olor dulce en el motor',4],
    ['termostato','Temperatura errática',1],['termostato','Motor tarda en calentar',2],
    ['termostato','Sobrecalentamiento súbito',3],['termostato','Calefacción deficiente',4],
    ['bomba-agua','Fugas cerca de la bomba',1],['bomba-agua','Ruido de rodamiento',2],
    ['bomba-agua','Sobrecalentamiento',3],['bomba-agua','Refrigerante contaminado',4],
    ['refrigerante','Nivel bajo recurrentemente',1],['refrigerante','Color marrón u oscuro',2],
    ['refrigerante','Motor con tendencia al sobrecalentamiento',3],
    ['catalizador','Olor a huevo podrido',1],['catalizador','Pérdida de potencia',2],
    ['catalizador','Check engine encendido',3],['catalizador','Fallo en prueba de emisiones',4],
    ['silenciador','Escape muy ruidoso',1],['silenciador','Ruido metálico bajo el auto',2],
    ['silenciador','Vibraciones en chasis',3],
    ['sensor-lambda','Check engine',1],['sensor-lambda','Consumo de combustible elevado',2],
    ['sensor-lambda','Emisiones altas',3],['sensor-lambda','Motor inestable en ralentí',4],
    ['tuberia-escape','Ruido de escape inusual',1],['tuberia-escape','Olor a gases en cabina',2],
    ['tuberia-escape','Vibraciones',3],['tuberia-escape','Corrosión visible',4],
];
foreach ($compSymptoms as $cs) { $csStmt->execute($cs); }
log_ok('Síntomas de componentes insertados ('.count($compSymptoms).')');

// ════════════════════════════════════════════════
// COMPONENT TIPS
// ════════════════════════════════════════════════
$tipStmt = $db->prepare('INSERT INTO component_tips (component_slug,tip,sort_order) VALUES (?,?,?)');
$tips = [
    ['pastillas-freno','Revisar el nivel del líquido de frenos regularmente.',1],
    ['pastillas-freno','No esperes al indicador de desgaste para revisar las pastillas.',2],
    ['pastillas-freno','Reemplazar en pares (eje delantero o trasero completo).',3],
    ['disco-freno','Inspeccionar en cada cambio de pastillas.',1],
    ['disco-freno','Nunca enfriar con agua cuando está caliente.',2],
    ['disco-freno','Reemplazar si tiene ranuras profundas o deformación.',3],
    ['pinzas-freno','Revisar fugas en cada mantenimiento.',1],
    ['pinzas-freno','No forzar el pistón sin purgar el sistema.',2],
    ['pinzas-freno','Lubricar los guías regularmente.',3],
    ['liquido-frenos','Verificar el nivel mensualmente.',1],
    ['liquido-frenos','No mezclar tipos de líquido DOT.',2],
    ['liquido-frenos','Cambiar cada 2 años aunque no esté oscuro.',3],
    ['cilindro-maestro','Inspeccionar ante pedal blando persistente.',1],
    ['cilindro-maestro','No reparar internamente — reemplazar completo.',2],
    ['cilindro-maestro','Verificar el depósito de líquido regularmente.',3],
    ['servofreno','Probar con motor apagado: el pedal se debe endurecer tras 2 pisadas.',1],
    ['servofreno','No usar el vehículo sin servofreno funcional.',2],
    ['servofreno','Revisar la manguera de vacío si el pedal está duro.',3],
    ['filtro-aire','Revisar visualmente en cada cambio de aceite.',1],
    ['filtro-aire','En zonas de polvo, cambiar con más frecuencia.',2],
    ['filtro-aire','No limpiar con agua — puede dañarlo.',3],
    ['bujias','Reemplazar en el intervalo indicado por el fabricante.',1],
    ['bujias','Usar la especificación exacta del manual.',2],
    ['bujias','No apretar en exceso al instalar.',3],
    ['correa-distribucion','Cambiar en el intervalo indicado sin excepción.',1],
    ['correa-distribucion','Reemplazar también la bomba de agua y tensores.',2],
    ['correa-distribucion','Una correa rota puede destruir el motor.',3],
    ['bomba-combustible','Mantener el tanque por encima del 25% para enfriar la bomba.',1],
    ['bomba-combustible','Usar combustible de calidad.',2],
    ['bomba-combustible','Reemplazar el filtro con la bomba.',3],
    ['sensor-oxigeno','No ignorar el check engine — diagnosticar con escáner.',1],
    ['sensor-oxigeno','Reemplazar en pares si hay dos sensores.',2],
    ['sensor-oxigeno','Evitar combustible con plomo.',3],
    ['filtro-combustible','Cambiar con la frecuencia indicada.',1],
    ['filtro-combustible','En filtros externos, aliviar presión antes de cambiar.',2],
    ['filtro-combustible','Usar filtros de calidad certificada.',3],
    ['amortiguadores','Probar presionando fuerte cada esquina: no debe rebotar más de una vez.',1],
    ['amortiguadores','Reemplazar en pares (eje completo).',2],
    ['amortiguadores','Inspeccionar sellos por fugas de aceite.',3],
    ['resortes','Reemplazar si hay deformación o altura desigual.',1],
    ['resortes','No cortar resortes para bajar el auto.',2],
    ['resortes','Instalar con protección antirruido.',3],
    ['rotulas','Revisar el juego axial y radial regularmente.',1],
    ['rotulas','Engrasar en modelos con niple de engrase.',2],
    ['rotulas','No ignorar chasquidos — pueden ser peligrosos.',3],
    ['barra-estabilizadora','Los bujes de la barra se desgastan antes que la barra.',1],
    ['barra-estabilizadora','Reemplazar bujes si hay ruido al pasar baches.',2],
    ['barra-estabilizadora','Verificar los links de la barra en cada mantenimiento.',3],
    ['bateria','Revisar terminales por corrosión.',1],
    ['bateria','Reemplazar cada 3–4 años preventivamente.',2],
    ['bateria','No dejar luces encendidas con motor apagado.',3],
    ['alternador','Verificar el voltaje de carga con multímetro.',1],
    ['alternador','Revisar la banda de alternador.',2],
    ['alternador','Un alternador deficiente daña las baterías.',3],
    ['motor-arranque','Un clic suele indicar solenoide defectuoso.',1],
    ['motor-arranque','Verificar la batería antes de culpar al arranque.',2],
    ['motor-arranque','Revisar el cableado de alta corriente.',3],
    ['fusibles','Llevar fusibles de repuesto en el vehículo.',1],
    ['fusibles','Si un fusible se funde repetidamente, hay un cortocircuito.',2],
    ['fusibles','No reemplazar por uno de mayor amperaje.',3],
    ['radiador','Revisar el refrigerante en frío mensualmente.',1],
    ['radiador','Limpiar las aletas del radiador cada año.',2],
    ['radiador','No abrir el tapón en caliente.',3],
    ['termostato','Reemplazar preventivamente cada 80,000 km.',1],
    ['termostato','Siempre reemplazar con el kit completo (junta incluida).',2],
    ['termostato','Usar termostato OEM para mantener temperatura correcta.',3],
    ['bomba-agua','Cambiar junto con la correa de distribución.',1],
    ['bomba-agua','Revisar el impulsor dentro de la bomba.',2],
    ['bomba-agua','Una bomba deficiente puede sobrecalentar en minutos.',3],
    ['refrigerante','Usar el tipo específico del fabricante (verde, rojo, azul).',1],
    ['refrigerante','No mezclar tipos de refrigerante.',2],
    ['refrigerante','Desechar correctamente — es tóxico para animales.',3],
    ['catalizador','No ignorar el sensor de oxígeno — puede dañar el catalizador.',1],
    ['catalizador','Evitar aceite o refrigerante en la combustión.',2],
    ['catalizador','No golpear el fondo del vehículo.',3],
    ['silenciador','Revisar en levantamiento en cada mantenimiento.',1],
    ['silenciador','Las perforaciones son peligrosas — permiten CO2 en cabina.',2],
    ['silenciador','Reemplazar si hay golpes o perforaciones.',3],
    ['sensor-lambda','Diagnosticar con escáner antes de reemplazar.',1],
    ['sensor-lambda','Verificar el cableado antes del sensor.',2],
    ['sensor-lambda','Usar llave de sensor para evitar daños.',3],
    ['tuberia-escape','Inspeccionar junturas y abrazaderas.',1],
    ['tuberia-escape','Los gases de escape son letales en cabina cerrada.',2],
    ['tuberia-escape','El óxido avanzado requiere reemplazo de sección.',3],
];
foreach ($tips as $t) { $tipStmt->execute($t); }
log_ok('Consejos insertados ('.count($tips).')');

// ════════════════════════════════════════════════
// DIAGNOSTIC RESULTS
// ════════════════════════════════════════════════
$drStmt = $db->prepare(
    'INSERT INTO diagnostic_results (numeric_id,slug,title,description,priority,system_slug) VALUES (?,?,?,?,?,?)'
);
$results = [
    ['1','pastillas-freno-desgastadas','Pastillas de Freno Desgastadas',
     'El chirrido metálico al frenar indica que las pastillas han llegado al límite de desgaste. Requiere atención pronto.',
     'Alta','frenos'],
    ['2','disco-freno-irregular','Disco de Freno con Irregularidades',
     'Los discos rayados o deformados causan vibración en el pedal y ruido al frenar a bajas velocidades.',
     'Media','frenos'],
    ['3','polvo-oxido-frenos','Acumulación de Polvo u Óxido',
     'Después de lluvia o períodos sin uso, es normal un breve chirrido al frenar que desaparece tras pocos usos.',
     'Baja','frenos'],
    ['4','correa-distribucion-suelta','Correa de Distribución Suelta',
     'Un ruido de golpeteo al arrancar puede indicar desgaste o tensión insuficiente en la correa de distribución.',
     'Alta','motor'],
    ['5','amortiguadores-desgastados','Amortiguadores Desgastados',
     'La vibración excesiva al girar o en curvas indica que los amortiguadores no absorben correctamente los impactos.',
     'Media','suspension'],
    ['6','bateria-carga-baja','Batería con Carga Baja',
     'El motor gira lento al arrancar o las luces parpadean, indicando que la batería no mantiene carga suficiente.',
     'Media','electrico'],
    ['7','catalizador-obstruido','Catalizador Obstruido',
     'Pérdida de potencia progresiva con olor a huevo puede señalar obstrucción del catalizador.',
     'Media','escape'],
    ['8','termostato-defectuoso','Termostato Defectuoso',
     'La temperatura sube y baja erráticamente porque el termostato no regula correctamente el flujo del refrigerante.',
     'Alta','enfriamiento'],
];
foreach ($results as $r) { $drStmt->execute($r); }
log_ok('Diagnósticos insertados ('.count($results).')');

// ── Tags ────────────────────────────────────────
$tagStmt = $db->prepare('INSERT INTO diagnostic_tags (result_slug,tag,sort_order) VALUES (?,?,?)');
$tags = [
    ['pastillas-freno-desgastadas','Frenos',1],['pastillas-freno-desgastadas','Pastillas',2],['pastillas-freno-desgastadas','Ruido',3],
    ['disco-freno-irregular','Frenos',1],['disco-freno-irregular','Disco',2],['disco-freno-irregular','Vibración',3],
    ['polvo-oxido-frenos','Frenos',1],['polvo-oxido-frenos','Normal',2],
    ['correa-distribucion-suelta','Motor',1],['correa-distribucion-suelta','Correa',2],['correa-distribucion-suelta','Ruido',3],
    ['amortiguadores-desgastados','Suspensión',1],['amortiguadores-desgastados','Vibración',2],
    ['bateria-carga-baja','Eléctrico',1],['bateria-carga-baja','Batería',2],['bateria-carga-baja','Arranque',3],
    ['catalizador-obstruido','Escape',1],['catalizador-obstruido','Catalizador',2],['catalizador-obstruido','Olor',3],
    ['termostato-defectuoso','Enfriamiento',1],['termostato-defectuoso','Termostato',2],
];
foreach ($tags as $t) { $tagStmt->execute($t); }
log_ok('Tags insertados ('.count($tags).')');

// ── Zones ───────────────────────────────────────
$zoneStmt = $db->prepare('INSERT INTO diagnostic_zones (result_slug,zone) VALUES (?,?)');
$zones = [
    ['pastillas-freno-desgastadas','Ruedas delanteras'],
    ['disco-freno-irregular','Ruedas delanteras'],
    ['polvo-oxido-frenos','Ruedas delanteras'],
    ['correa-distribucion-suelta','Motor'],
    ['amortiguadores-desgastados','Suspensión'],
    ['bateria-carga-baja','Eléctrico'],
    ['catalizador-obstruido','Escape'],
    ['termostato-defectuoso','Motor'],
];
foreach ($zones as $z) { $zoneStmt->execute($z); }
log_ok('Zonas insertadas ('.count($zones).')');

// ── When ────────────────────────────────────────
$whenStmt = $db->prepare('INSERT INTO diagnostic_when (result_slug,when_text) VALUES (?,?)');
$whens = [
    ['pastillas-freno-desgastadas','Al frenar'],
    ['disco-freno-irregular','Al frenar'],
    ['polvo-oxido-frenos','Al frenar'],
    ['correa-distribucion-suelta','Al arrancar'],
    ['amortiguadores-desgastados','Al girar'],
    ['amortiguadores-desgastados','En curvas'],
    ['bateria-carga-baja','Al arrancar'],
    ['catalizador-obstruido','En ralentí'],
    ['catalizador-obstruido','A alta velocidad'],
    ['termostato-defectuoso','En ralentí'],
    ['termostato-defectuoso','A alta velocidad'],
];
foreach ($whens as $w) { $whenStmt->execute($w); }
log_ok('Cuándo insertados ('.count($whens).')');

// ── Sensations ──────────────────────────────────
$sensStmt = $db->prepare('INSERT INTO diagnostic_sensations (result_slug,sensation) VALUES (?,?)');
$sensations = [
    ['pastillas-freno-desgastadas','Fricción'],['pastillas-freno-desgastadas','Ruido'],
    ['disco-freno-irregular','Vibración'],['disco-freno-irregular','Ruido'],
    ['polvo-oxido-frenos','Ruido'],
    ['correa-distribucion-suelta','Ruido'],
    ['amortiguadores-desgastados','Vibración'],
    ['catalizador-obstruido','Humo'],['catalizador-obstruido','Olor'],
    ['termostato-defectuoso','Calor'],
];
foreach ($sensations as $s) { $sensStmt->execute($s); }
log_ok('Sensaciones insertadas ('.count($sensations).')');

echo '<h2>¡Importación completada!</h2>';
echo '<p>Todos los datos han sido insertados correctamente en la base de datos <strong>carsense</strong>.</p>';
echo '<p><a href="index.html" style="color:#e03030">← Volver a la aplicación</a></p>';
?>
</body>
</html>
