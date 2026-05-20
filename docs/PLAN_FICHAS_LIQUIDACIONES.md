# Sistema de Fichas, Liquidaciones y Comisiones
## Documento Funcional

---

## Roles y Jerarquía

```
ADMIN (dueño del sistema)
  └── VENDOR (dueño de marca/casino)
        └── ENCARGADO (supervisor de línea)
              └── AGENTE (opera día a día, atiende clientes)
                    └── CLIENTE (jugador)
```

**Agente:** Es quien está en la trinchera. Atiende clientes por WhatsApp, carga fichas, descarga fichas, registra netloss. Coloquialmente le dicen "cajero" pero en el sistema es **Agente**.

**Encargado:** Supervisa uno o más agentes. Aprueba o rechaza sus liquidaciones. Ve el consolidado de su línea.

**Vendor:** Dueño de la marca. Ve todo su vendor: todas las líneas, todos los encargados, todos los agentes.

**Admin:** Acceso total al sistema, todos los vendors.

---

## 1. Cargar Fichas

**Quién lo hace:** Agente

**Cuándo:** Cuando un cliente le manda plata (transferencia, efectivo, MercadoPago)

**Cómo funciona:**
1. El agente entra al sistema y aprieta **"Cargar Fichas"**
2. Busca al cliente por nombre
3. Pone cuánto dinero recibió (ej: $100.000)
4. El sistema calcula automáticamente cuántas fichas son (la relación pesos/fichas está configurada por línea, ej: 1 ficha = $100)
5. Si hay bono (ej: 10%), el sistema agrega fichas gratis automáticamente (100 fichas extra → total 1.100)
6. El agente sube la foto de la transferencia como comprobante
7. Guarda

**Qué queda registrado:**
- Qué cliente cargó, cuánto dinero, cuántas fichas, si tuvo bono, qué comprobante hay, a qué hora fue
- El saldo de fichas del cliente se actualiza solo

---

## 2. Descargar Fichas (Retiro)

**Quién lo hace:** Agente

**Cuándo:** Cuando un cliente quiere retirar plata (porque ganó o porque le quedan fichas sin usar)

**Cómo funciona:**
1. El agente entra al sistema y aprieta **"Descargar Fichas"**
2. Busca al cliente → el sistema muestra cuántas fichas tiene disponibles
3. El agente pone cuántas fichas quiere descargar
4. El sistema calcula cuánto dinero hay que pagarle
5. El agente le transfiere al cliente y sube el comprobante
6. Guarda

**Qué queda registrado:**
- Qué cliente retiró, cuántas fichas, cuánta plata se le pagó, comprobante
- El saldo del cliente baja automáticamente

---

## 3. Registrar Netloss (Pérdida del Cliente)

**Quién lo hace:** Agente

**Cuándo:** Cuando el cliente jugó en la plataforma y perdió

**Cómo funciona:**
1. El agente entra a **"Netloss"**
2. Busca al cliente
3. Registra:
   - Cuánto perdió en pesos
   - En qué plataforma perdió
   - Qué día jugó
   - Una nota (ej: "perdió en ruleta")
4. Guarda

**Qué queda registrado:**
- Cada pérdida de cada cliente
- El netloss acumulado por cliente y por día
- Esto es lo que realmente genera la ganancia de la cadena

---

## 4. Caja del Agente (Vista Rápida del Día)

**Quién lo ve:** Agente

**Qué muestra:**
- Cuánto dinero recibió hoy por cargas
- Cuánto dinero pagó hoy por descargas
- Cuánto netloss generaron sus clientes hoy
- Cuánto tiene en caja (recibido - pagado)
- Lista de los últimos movimientos con hora y cliente

Es como mirar la caja registradora pero digital. Un vistazo rápido de cómo está el día.

---

## 5. Cierre de Período (Liquidación)

**Quién genera:** Agente
**Quién aprueba:** Encargado
**Quién ve todo:** Vendor

### Paso 1: El agente genera la liquidación
- Elige el período (diario, semanal, quincenal)
- El sistema calcula TODO automáticamente:

```
LIQUIDACIÓN - 18/05/2026
Agente: Juan Pérez
Línea: WhatsApp Casino

CARGAS DEL DÍA:
  Total recibido: $500.000 (5.000 fichas)
  Fichas de bono regaladas: 500

DESCARGAS DEL DÍA:
  Total pagado: $100.000 (1.000 fichas)

NETLOSS:
  Pérdida total de clientes: $350.000

COMISIONES (sobre el netloss):
  Agente se queda (80%):    $280.000
  Encargado cobra (15%):     $52.500
  Vendor cobra (5%):         $17.500

A ENTREGAR AL ENCARGADO: $70.000
```

### Paso 2: El agente revisa y envía
- Verifica que los números estén bien
- Sube el comprobante de la transferencia que le hace al encargado
- Aprieta "Enviar para aprobación"

### Paso 3: El encargado recibe notificación
- Ve la liquidación del agente
- Puede ver cada movimiento que la compone
- Si está todo bien → aprueba
- Si hay algo raro → rechaza con observación

### Paso 4: Queda cerrado
- La liquidación pasa a estado "aprobado"
- Ya no se puede modificar
- Las comisiones quedan registradas para pago

### Estados de una liquidación
| Estado | Qué significa |
|---|---|
| Borrador | El agente la está armando, puede editar |
| Pendiente | Enviada al encargado, esperando aprobación |
| Aprobado | El encargado la revisó y está OK |
| Pagado | Se hizo la transferencia, ciclo cerrado |
| Rechazado | El encargado la rechazó, el agente debe corregir |

---

## 6. Cadena de Comisiones (Cómo se Reparte la Plata)

### La regla

Cada venta tiene una torta que se reparte entre tres niveles:

```
Cliente carga $100.000 en fichas
         ↓
    ┌────────────────────┐
    │  Agente: 80%       │  → $80.000 para el que vendió
    │  Encargado: 15%    │  → $15.000 para el que lo supervisa
    │  Vendor: 5%        │  → $5.000 para el dueño de la marca
    └────────────────────┘
```

### Pero la comisión real se calcula sobre el NETLOSS

```
Clientes perdieron $350.000 en el día
         ↓
    ┌────────────────────┐
    │  Agente: 80%       │  → $280.000 (lo que se queda)
    │  Encargado: 15%    │  → $52.500 (lo que cobra por respaldar)
    │  Vendor: 5%        │  → $17.500 (lo que cobra por la marca)
    └────────────────────┘
```

El agente tiene que entregarle $70.000 al encargado (15% + 5% del netloss).

### Los porcentajes son configurables

Cada línea puede tener sus propios porcentajes. Se configuran una vez y el sistema los aplica automáticamente.

---

## 7. Seguimiento por Cliente

**Quién lo ve:** Agente, Encargado, Vendor

**Qué se puede ver de cada cliente:**
- Cuántas fichas tiene ahora (saldo)
- Histórico de todas sus cargas (cuándo, cuánto, con qué comprobante)
- Histórico de todos sus retiros
- Cuánto perdió en total (netloss acumulado)
- En qué plataformas juega más
- Cuánto bono recibió

---

## 8. Alertas Automáticas

El sistema avisa cuando:
- Un cliente tiene un netloss muy alto (posible problema)
- Un agente mueve mucho más o mucho menos de lo normal
- Una liquidación está pendiente de aprobación hace mucho
- Un cliente se queda sin fichas (oportunidad para ofrecer carga)

---

## 9. Integración con WhatsApp

### ¿Por qué?

WhatsApp es el canal de comunicación real. El sistema es el registro. La integración cierra el loop.

### Nivel 1: Notificaciones automáticas (prioridad alta)

El sistema manda mensajes solos cuando pasan cosas importantes:

**Al Encargado:**
- "El agente Juan Pérez generó liquidación del 18/05. Netloss: $350.000. Revisá: [link]"
- "Alerta: El agente María tuvo un netloss 3x mayor al promedio"
- "Reporte diario: 4 agentes activos, $1.2M cargas, $750K netloss"

**Al Agente:**
- "Tenés una liquidación pendiente de aprobación"
- "El encargado rechazó tu liquidación del 18/05. Motivo: ..."
- "El cliente Juan tiene saldo bajo (50 fichas). ¿Querés ofrecerle carga?"

**Al Cliente (opcional):**
- "Tu carga de 1.100 fichas fue registrada. Saldo actual: 1.100 fichas"
- "Tu retiro de $20.000 fue procesado"

### Nivel 2: Bot de comandos (prioridad media)

El agente puede hacer cosas desde WhatsApp sin entrar al sistema:

| Comando | Qué hace |
|---|---|
| `carga [cliente] [monto]` | Pre-registra una carga |
| `descarga [cliente] [fichas]` | Pre-registra una descarga |
| `netloss [cliente] [monto]` | Pre-registra un netloss |
| `caja` | Manda el resumen del día |
| `saldo [cliente]` | Dice cuántas fichas tiene |
| `liquidacion` | Genera borrador y manda resumen |

El comprobante (foto de transferencia) se sube desde el sistema. El bot pre-registra y genera un link para completar.

### Nivel 3: Recepción de comprobantes por WhatsApp (prioridad baja)

El agente le manda la foto del comprobante al bot y el sistema la asocia automáticamente a la última operación pendiente.

### Qué NO cambia

- Los clientes siguen escribiendo por WhatsApp como siempre
- Los pagos siguen siendo por transferencia/efectivo
- WhatsApp solo notifica y facilita, no reemplaza nada

---

## Resumen Visual de Todo el Flujo

```
Cliente paga $ → Agente CARGA fichas → Cliente juega
                                              ↓
                                       Pierde → Agente registra NETLOSS
                                       Gana  → Agente DESCARGA fichas
                                              ↓
                                    Fin del día → CAJA (resumen rápido)
                                              ↓
                                 Agente genera LIQUIDACIÓN
                                              ↓
                                 Encargado APRUEBA o rechaza
                                              ↓
                                 Se paga → COMISIONES repartidas
                                 (Agente / Encargado / Vendor)
```

Todo lo que hoy pasa por WhatsApp y memoria, queda registrado con comprobante, fecha, y números que se calculan solos.

---

## Relación Peso/Fichas

Cada línea tiene configurada cuántos pesos vale una ficha. Ejemplo:

| Línea | 1 ficha = |
|---|---|
| WhatsApp Casino | $100 |
| Telegram VIP | $50 |
| Discord Pro | $200 |

El sistema usa esta relación para calcular automáticamente:
- Cuántas fichas corresponden al monto que recibió el agente
- Cuánto dinero pagar al descargar fichas
- El bono en fichas (si aplica)

---

## Permisos: Qué Puede Hacer Cada Rol

| Función | Agente | Encargado | Vendor | Admin |
|---|---|---|---|---|
| Cargar fichas | ✅ Su línea | ✅ Su línea | ✅ Todo | ✅ Todo |
| Descargar fichas | ✅ Su línea | ✅ Su línea | ✅ Todo | ✅ Todo |
| Registrar netloss | ✅ Su línea | ✅ Su línea | ✅ Todo | ✅ Todo |
| Ver caja | ✅ Su caja | ✅ Su línea | ✅ Todo | ✅ Todo |
| Generar liquidación | ✅ La suya | ✅ Ver todas | ✅ Ver todas | ✅ Ver todas |
| Aprobar liquidación | ❌ | ✅ Las de sus agentes | ✅ Todas | ✅ Todas |
| Ver comisiones | ✅ Las suyas | ✅ Su línea | ✅ Todo | ✅ Todo |
| Ver seguimiento cliente | ✅ Sus clientes | ✅ Su línea | ✅ Todo | ✅ Todo |
| Configurar porcentajes | ❌ | ❌ | ✅ Su vendor | ✅ Todo |
| Ver reportes globales | ❌ | ❌ | ✅ Su vendor | ✅ Todo |

---

## Reglas de Negocio

1. **Una liquidación aprobada no se puede editar.** Si hay un error, se genera un ajuste en el próximo período.

2. **El netloss se calcula sobre lo que el cliente perdió**, no sobre lo que cargó. Las comisiones se pagan sobre el netloss, no sobre las cargas.

3. **El saldo de fichas del cliente es por línea.** Un cliente puede tener 500 fichas en una línea y 200 en otra.

4. **Los bonos son fichas gratis que se suman al saldo.** No se descuentan de ningún lado, son incentivo.

5. **El agente solo ve sus propios clientes y su propia línea.** El encargado ve todos los agentes de su línea. El vendor ve todo su vendor.

6. **Los comprobantes son obligatorios para cerrar una liquidación.** Sin comprobante de transferencia, la liquidación no puede pasar a "pagado".

7. **Los porcentajes de comisión suman 100%.** Si el agente se queda con 80%, el encargado con 15% y el vendor con 5%, no puede haber un porcentaje de más ni de menos.

---

## Glosario

| Término | Significado |
|---|---|
| **Ficha** | Unidad de juego. Equivale a un monto en pesos según la línea |
| **Carga** | Cuando un cliente compra fichas (entra plata) |
| **Descarga** | Cuando un cliente retira fichas (sale plata) |
| **Netloss** | Pérdida neta del cliente. Es la ganancia real de la cadena |
| **Bono** | Fichas gratis que se regalan como incentivo (porcentaje sobre la carga) |
| **Liquidación** | Cierre de período donde se calculan comisiones y quién le debe a quién |
| **Caja** | Resumen rápido del día: recibido, pagado, netloss, saldo |
| **Comisión** | Porcentaje que le corresponde a cada nivel de la cadena |
| **Encargado** | Supervisor de agentes. Aprueba liquidaciones |
| **Agente** | Quien opera día a día, atiende clientes, carga/descarga fichas |
