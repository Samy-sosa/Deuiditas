<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box;
            font-weight: bold !important; /* TODO EN NEGRITAS */
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 100%;
            font-size: 11px; /* Letra un poco más grande */
            background: white;
            color: #000;
        }
        
        .ticket {
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            padding: 1.5mm;
        }
        
        .cliente-grande {
            text-align: center;
            margin-bottom: 6px;
            padding: 5px;
            border: 2px solid #000;
        }
        
        .cliente-nombre {
            font-size: 16px;
            text-transform: uppercase;
        }
        
        .codigo-destacado {
            text-align: center;
            margin-bottom: 6px;
            padding: 5px;
            background: #000;
            color: #fff;
        }
        
        .codigo-valor { font-size: 14px; }
        
        .separator { border-top: 2px solid #000; margin: 6px 0; }
        
        .producto-item { 
            margin-bottom: 6px; 
            padding-left: 2px; 
            border-left: 3px solid #000; 
        }
        
        .producto-nombre { font-size: 12px; }
        .producto-detalle { font-size: 11px; }
        
        .fecha-vencimiento {
            text-align: center;
            margin: 6px 0;
            padding: 6px;
            border: 2px solid #000;
        }
        
        .fecha-label { font-size: 9px; margin-bottom: 2px; }
        .fecha-valor { font-size: 14px; }
        
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 6px;
        }

        /* RELLENO DE SEGURIDAD PARA EL CORTE */
        .relleno-corte {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="cliente-grande">
            <div class="cliente-nombre">{{ strtoupper($apartado->nombre_cliente) }}</div>
            <div class="cliente-label">CLIENTE</div>
        </div>
        
        <div class="codigo-destacado">
            <div class="codigo-valor">{{ $apartado->codigo_unico }}</div>
        </div>
        
        <div class="separator"></div>
        
        <div class="productos-lista">
            @if(isset($productos_especificos) && count($productos_especificos) > 0)
                @foreach($productos_especificos as $producto)
                <div class="producto-item">
                    <div class="producto-nombre">{{ strtoupper($producto['nombre']) }}</div>
                    <div class="producto-detalle">{{ $producto['cantidad'] }} x ${{ number_format($producto['precio'], 2) }}</div>
                </div>
                @endforeach
            @else
                @foreach($apartado->productos as $producto)
                <div class="producto-item">
                    <div class="producto-nombre">{{ strtoupper($producto->nombre_producto) }}</div>
                    <div class="producto-detalle">{{ $producto->cantidad }} x ${{ number_format($producto->precio_unitario, 2) }}</div>
                </div>
                @endforeach
            @endif
        </div>
        
        <div class="separator"></div>
        
        <div class="fecha-vencimiento">
            <div class="fecha-label">FECHA LIMITE PARA RECOGER</div>
            <div class="fecha-valor">{{ \Carbon\Carbon::parse($apartado->fecha_limite)->format('d/m/Y') }}</div>
        </div>
        
        <div class="footer">
            {{ $tienda->nombre_tienda ?? 'Tienda' }}<br>
            {{ date('d/m/Y H:i') }}
        </div>

        <div class="relleno-corte">
            .<br>.<br>.<br>.<br>.<br>.<br>.<br>.<br>.<br>.
        </div>
    </div>
    
<script>
function imprimirDirecto(urlPdf) {
    const ventana = window.open(urlPdf, '_blank');

    ventana.onload = function () {
        ventana.focus();
        ventana.print();
    };
}
</script>
</body>
</html>