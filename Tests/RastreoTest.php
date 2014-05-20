<?php

# TODO: Mucho codigo en los tests, refactor
require_once 'vendor/autoload.php';
require_once 'Estafeta.php';

use Ivansabik\DomHunter\DomHunter;
use Ivansabik\DomHunter\KeyValue;
use Ivansabik\DomHunter\IdUnico;
use Ivansabik\DomHunter\NodoDom;
use Ivansabik\DomHunter\Tabla;

class RastreoTest extends PHPUnit_Framework_TestCase {

    private $_hunter, $_hunted;
    private static $_assert = array(
        'numero_guia' => '6058277365651709009524',
        'codigo_rastreo' => '0738266367',
        'servicio' => 'Entrega garantizada de 2 a 5 días hábiles (según distancia)',
        'fecha_programada' => 'Ocurre. El destinatario cuenta con 10 días hábiles para recoger su envío, una vez que este haya sido entregado en la plaza destino.',
        'origen' => 'MEXICO D.F.',
        'cp_destino' => '55230',
        'fecha_recoleccion' => '21/11/2013 05:32 PM',
        'destino' => 'Estación Aérea MEX',
        'estatus' => 'Entregado',
        'fecha_entrega' => '28/11/2013 02:30 PM',
        'tipo_envio' => 'PAQUETE',
        'recibio' => 'PDV2:LASCAREZ MARTINEZ ENRIQUE',
        'firma_recibido' => '',
        'peso' => '0.2',
        'peso_volumetrico' => '0.0',
        'peso_volumetrico' => '0.0',
        'peso_volumetrico' => '0.0',
        'movimientos' => array(
            array('fecha' => '28/11/2013 02:30 PM', 'descripcion' => 'Recolección en oficina por ruta local', 'id' => 4),
            array('fecha' => '28/11/2013 12:19 PM', 'descripcion' => 'Envío recibido en oficina', 'id' => 5),
            array('fecha' => '27/11/2013 07:18 PM', 'descripcion' => 'Carga Aerea AER Aclaración en proceso', 'id' => 7),
            array('fecha' => '27/11/2013 04:18 PM', 'descripcion' => 'Envío recibido en oficina', 'id' => 5),
            array('fecha' => '27/11/2013 09:19 AM', 'descripcion' => 'Estación Aérea MEX En proceso de entrega Av Central 161 Impulsora Popular Avicola Nezahualcoyotl', 'id' => 1),
            array('fecha' => '27/11/2013 07:24 AM', 'descripcion' => 'Estación Aérea MEX Llegada a centro de distribución AMX Estación Aérea MEX', 'id' => 2),
            array('fecha' => '26/11/2013 07:05 PM', 'descripcion' => 'MEXICO D.F. En ruta foránea hacia MX2-México (zona 2)', 'id' => 3),
            array('fecha' => '26/11/2013 07:03 PM', 'descripcion' => 'MEXICO D.F. Llegada a centro de distribución MEX MEXICO D.F.', 'id' => 2),
            array('fecha' => '26/11/2013 06:55 PM', 'descripcion' => 'MEXICO D.F. Movimiento en centro de distribución', 'id' => 6),
            array('fecha' => '26/11/2013 06:55 PM', 'descripcion' => 'MEXICO D.F. Movimiento en centro de distribución', 'id' => 6),
            array('fecha' => '26/11/2013 03:17 PM', 'descripcion' => 'MEXICO D.F. Aclaración en proceso', 'id' => 7),
            array('fecha' => '23/11/2013 10:42 AM', 'descripcion' => 'MEXICO D.F. Movimiento Local', 'id' => 9),
            array('fecha' => '22/11/2013 12:00 PM', 'descripcion' => 'MEXICO D.F. Movimiento en centro de distribución', 'id' => 6),
            array('fecha' => '22/11/2013 11:33 AM', 'descripcion' => 'MEXICO D.F. Movimiento Local', 'id' => 9),
            array('fecha' => '22/11/2013 11:05 AM', 'descripcion' => 'MEXICO D.F. Movimiento en centro de distribución', 'id' => 6),
            array('fecha' => '22/11/2013 10:58 AM', 'descripcion' => 'MEXICO D.F. Movimiento Local', 'id' => 9),
            array('fecha' => '21/11/2013 07:00 PM', 'descripcion' => 'MEXICO D.F. Movimiento en centro de distribución', 'id' => 6),
            array('fecha' => '21/11/2013 06:56 PM', 'descripcion' => 'MEXICO D.F. Llegada a centro de distribución MEX MEXICO D.F.', 'id' => 2),
        )
    );

    protected function setUp() {
        $html = '<table width="88%" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td width="13" height="58" valign="top" background="http://www.estafeta.com/imagenes/lineageneralizquierda.png">&nbsp;</td><td width="664" valign="middle" background="http://www.estafeta.com/imagenes/medioazulgeneral.png"><div align="right"><img src="http://www.estafeta.com/imagenes/herramientas.png" width="333" height="31"></div></td><td width="11" valign="top" background="http://www.estafeta.com/imagenes/derechaazulgeneral.png">&nbsp;</td></tr><tr><td height="33" colspan="3" valign="top" background="http://www.estafeta.com/imagenes/separaseccroja.jpg"><div align="left"><img src="http://www.estafeta.com/imagenes/rastreotitulo.png" width="258" height="27"></div></td></tr><tr><td colspan="3" valign="top"><p align="right"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="1"><a href="http://www.estafeta.com/herramientas/rastreo.aspx">Nueva consulta</a></font></p><div align="left"><form><table width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><table width="100%" border="0" cellpadding="1" cellspacing="1"><tbody><tr><td width="16%" height="30" bgcolor="#d6d6d6" class="titulos"><div align="left" class="titulos">Número de guía</div></td><td width="30%" colspan="2" bgcolor="#edf0e9" class="respuestas"><div align="left" class="respuestas"> 6058277365651709009524</div></td><td width="16%" bgcolor="#d6d6d6" class="titulos"><div align="left" class="titulos">Código de rastreo</div></td><td width="35%" colspan="3" bgcolor="#edf0e9" class="respuestas"><div align="left" class="respuestas">0738266367</div></td></tr></tbody></table></td></tr><tr><td><table width="100%" border="0" cellpadding="1" cellspacing="1"><tbody><tr><td width="16%" bgcolor="#d6e3f5" class="titulos"><div align="left">Origen</div></td><td width="30%" bgcolor="#edf0e9" class="respuestas"><div align="left">MEXICO D.F.</div></td><td width="16%" bgcolor="#d6e3f5" class="titulos"><div align="left" class="titulos">Destino</div></td><td width="35%" colspan="3" class="respuestas"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td height="30" width="50%" bgcolor="#edf0e9"><div align="left" class="respuestas">Estación Aérea MEX</div></td><td height="30" width="30%" bgcolor="#d6e3f5" class="titulos">CP Destino</td><td height="30" width="20%" bgcolor="#edf0e9" class="respuestas" align="left">&nbsp;55230</td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="16%" bgcolor="#d6d6d6" class="titulos"><div align="left">Estatus del servicio</div></td><td width="10%" bgcolor="#edf0e9" class="respuestasazul"><div align="left"><img src="images/palomita.png" border="0" width="50" height="50"></div></td><td width="20%" bgcolor="#edf0e9" class="respuestasazul"><div align="left">Entregado</div></td><td width="16%" height="20" bgcolor="#d6d6d6"><div class="titulos" align="left">Recibió</div></td><td width="25%" colspan="1" bgcolor="#edf0e9"><div class="respuestasazul3" align="left">PDV2:LASCAREZ MARTINEZ ENRIQUE </div></td><td width="10%" bgcolor="#edf0e9" class="style1"><div align="center"></div></td></tr></tbody></table></td></tr><tr><td align="center" bgcolor="edf0e9"><div class="msg_list"><p style="display: none;" id="6058277365651709009524FIR" class="style1 msg_head "></p><div class="msg_body"></div></div></td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="16%" height="30" bgcolor="#d6e3f5" class="titulos"><div align="left" class="titulos">Servicio</div></td><td width="30%" colspan="2" bgcolor="#edf0e9" class="respuestas"><div align="left" class="respuestas">Entrega garantizada de 2 a 5 días hábiles (según distancia)</div></td><td width="16%" height="30" bgcolor="#d6e3f5" class="titulos"><div align="left" class="titulos">Fecha y hora de entrega</div></td><td width="35%" colspan="3" bgcolor="#edf0e9" class="style1"><div align="left" class="respuestas">28/11/2013 02:30 PM</div></td></tr></tbody></table></td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="16%" height="30" bgcolor="#d6d6d6" class="titulos"><div align="left"><span class="titulos">Tipo de envío</span></div></td><td width="30%" colspan="2" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas">PAQUETE</span></div></td><td width="16%" bgcolor="#d6d6d6" height="30" class="titulos"><div align="left" class="titulos">Fecha programada <br>de entrega</div></td><td width="35%" colspan="3" bgcolor="#edf0e9" class="respuestas"><div align="left" class="respuestas">Ocurre. El destinatario cuenta con 10 días hábiles para recoger su envío, una vez que este haya sido entregado en la plaza destino.</div></td></tr></tbody></table></td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="16%" height="30" bgcolor="#d6e3f5" class="titulos"><div align="left"><span class="titulos">Número de orden de recolección</span></div></td><td width="30%" colspan="2" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas"><a href="http://rastreo2.estafeta.com/ShipmentPickUpWeb/actions/pickUpOrder.do?method=doGetPickUpOrder&amp;forward=toPickUpInfo&amp;idioma=es&amp;pickUpId=8058829" class="cuerpo" target="_blank">8058829</a></span></div></td><td width="16%" height="40" bgcolor="#d6e3f5" class="titulos"><div align="left" class="titulos">Fecha de recolección</div></td><td width="35%" colspan="3" bgcolor="#edf0e9" class="respuestas"><div align="left" class="respuestas"> 21/11/2013 05:32 PM</div></td></tr></tbody></table></td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="16%" height="30" bgcolor="#d6d6d6" class="titulos"><div align="left"><span class="titulos">Orden de rastreo*</span></div></td><td width="81%" colspan="6" height="30" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas">8111262177</span></div></td></tr><tr><td width="16%" height="30" bgcolor="#d6e3f5" class="titulos"><div align="left"><span class="titulos">Motivo*</span></div></td><td width="81%" colspan="6" height="30" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas">Coordinar a oficina Estafeta</span></div></td></tr><tr><td width="16%" height="30" bgcolor="#d6d6d6" class="titulos"><div align="left"><span class="titulos">Estatus*</span></div></td><td width="81%" colspan="6" height="30" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas">Concluidos</span></div></td></tr><tr><td width="16%" height="30" bgcolor="#d6e3f5" class="titulos"><div align="left"><span class="titulos">Resultado*</span></div></td><td width="81%" colspan="6" height="30" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas">Envio disponible en oficina Estafeta</span></div></td></tr></tbody></table></td></tr><tr><td><table width="690" align="center" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td class="pies"><div align="left"><span class="pies">*Aclaraciones acerca de su envío</span></div></td></tr></tbody></table></td></tr><tr><td>&nbsp;</td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="33%" rowspan="2" bgcolor="#d6d6d6" class="titulos"><div align="center"><span class="titulos">Guías envíos múltiples</span></div></td><td width="33%" bgcolor="#d6e3f5" class="titulos"><div align="center"><span class="titulos">Guía documento de retorno</span></div></td><td width="33%" bgcolor="#d6d6d6" class="titulos"><div align="center"><span class="titulos">Guía internacional</span></div></td></tr></tbody></table><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="33%" bgcolor="#edf0e9" class="style1"><div align="center">&nbsp;</div></td><td width="33%" bgcolor="#edf0e9" class="style1"><div align="center">&nbsp;</div></td><td width="33%" bgcolor="#edf0e9" class="repuestas"><div align="center">&nbsp;</div></td></tr></tbody></table></td></tr><tr><td>&nbsp;</td></tr><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="20%" rowspan="3" bgcolor="#d6e3f5" class="titulos"><div align="center"><span class="titulos">Características del envío</span><br><img onclick="darClick(\'6058277365651709009524CAR\')" src="images/caracter_envio.png" style="cursor:pointer;" border="0" width="75" height="75"></div></td><td width="20%" rowspan="3" bgcolor="#d6e3f5" class="titulos"><div align="center"><span class="titulos">Historia</span><br><img onclick="darClick(\'6058277365651709009524HIS\')" src="images/historia.png" style="cursor:pointer;" border="0" width="75" height="75"></div></td><td width="20%" rowspan="3" bgcolor="#d6e3f5" class="titulos"><div align="center"><span class="titulos">Preguntas frecuentes</span><br><a target="_blank" href="http://www.estafeta.com/herramientas/ayuda.aspx"><img src="images/preguntas.png" border="0" width="75" height="75"></a></div></td><td width="20%" rowspan="3" bgcolor="#d6e3f5" class="titulos"><div align="center"><span class="titulos">Comprobante de entrega</span><br><a target="_blank" href="/RastreoWebInternet/consultaEnvio.do?dispatch=doComprobanteEntrega&amp;guiaEst=6058277365651709009524"><img src="images/comprobante.png" border="0" width="75" height="75"></a></div></td></tr></tbody></table></td></tr><tr><td><div class="msg_list"><p style="display: none;" id="6058277365651709009524CAR" class="style1 msg_head "></p><div class="msg_body"><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="20%" rowspan="3" bgcolor="#d6d6d6" class="style1"><div align="center"><span class="titulos">Características </span></div></td></tr></tbody></table><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="16%" height="20" bgcolor="#d6e3f5" class="titulos"><div align="left"><span class="titulos">Tipo de envío</span></div></td><td width="30%" bgcolor="#edf0e9" class="respuestas"><div align="left"><span class="respuestas">PAQUETE</span></div></td><td width="20%" bgcolor="#d6e3f5" class="style1"><div align="left"><span class="titulos">Dimensiones cm</span></div></td><td width="30%" bgcolor="#edf0e9" class="style1"><div align="left"><span class="respuestas">0x0x0</span></div></td></tr><tr><td width="20%" bgcolor="#d6d6d6" class="style1"><div align="left"><span class="titulos">Peso kg</span></div></td><td width="30%" bgcolor="#edf0e9" class="style1"><div align="left"><span class="respuestas">0.2</span></div></td><td width="20%" bgcolor="#d6d6d6" class="style1"><div align="left"><span class="titulos">Peso volumétrico kg</span></div></td><td width="30%" bgcolor="#edf0e9" class="style1"><div align="left"><span class="respuestas">0.0</span></div></td></tr><tr><td width="20%" bgcolor="#d6e3f5" class="style1"><div align="left"><span class="titulos">Referencia cliente</span></div></td><td colspan="3" width="80%" bgcolor="#edf0e9" class="style1"><div align="center">&nbsp;</div></td></tr></tbody></table></div></div></td></tr><tr><td><div class="msg_list"><p style="display: none;" id="6058277365651709009524HIS" class="style1 msg_head "></p><div class="msg_body"><table width="100%" border="0" cellpadding="2" cellspacing="1"><tbody><tr><td width="20%" rowspan="3" bgcolor="#d6d6d6" class="style1"><div align="center"><span class="titulos">Historia </span></div></td></tr></tbody></table><table width="100%" border="0" cellpadding="0" cellspacing="1"><tbody><tr><td width="22%" height="23" bgcolor="#d6d6d6" class="titulos"><div align="center">Fecha - Hora</div></td><td width="31%" height="23" bgcolor="#d6d6d6" class="titulos"><div align="center">Lugar - Movimiento</div></td><td width="20%" height="23" bgcolor="#d6d6d6" class="titulos"><div align="center">Comentarios</div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">28/11/2013 02:30 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> Recolección en oficina por ruta local</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">&nbsp;</div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">28/11/2013 12:19 PM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> Envío recibido en oficina</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">Envío ocurre direccionado a oficina </div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">27/11/2013 07:18 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> Carga Aerea AER Aclaración en proceso</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Comunicarse al 01800 3782 338 </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">27/11/2013 04:18 PM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> Envío recibido en oficina</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">Envío ocurre direccionado a oficina </div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">27/11/2013 09:19 AM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> Estación Aérea MEX En proceso de entrega Av Central 161 Impulsora Popular Avicola Nezahualcoyotl</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Envío ocurre direccionado a oficina </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">27/11/2013 07:24 AM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> Estación Aérea MEX Llegada a centro de distribución AMX Estación Aérea MEX</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">&nbsp;</div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">26/11/2013 07:05 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> MEXICO D.F. En ruta foránea hacia MX2-México (zona 2)</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">&nbsp;</div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">26/11/2013 07:03 PM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> MEXICO D.F. Llegada a centro de distribución MEX MEXICO D.F.</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">&nbsp;</div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">26/11/2013 06:55 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento en centro de distribución</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Envío en proceso de entrega </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">26/11/2013 06:55 PM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento en centro de distribución</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">Recibe el área de Operaciones </div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">26/11/2013 03:17 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> MEXICO D.F. Aclaración en proceso</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Reporte generado por el cliente </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">23/11/2013 10:42 AM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento Local</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">Auditoria a ruta local </div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">22/11/2013 12:00 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento en centro de distribución</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Entrada a Control de Envíos </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">22/11/2013 11:33 AM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento Local</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">Entrega en Zona de Alto Riesgo y/o de difícil acceso </div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">22/11/2013 11:05 AM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento en centro de distribución</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Posible demora en la entrega por mal empaque </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">22/11/2013 10:58 AM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento Local</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">Entrega en Zona de Alto Riesgo y/o de difícil acceso </div></td></tr><tr><td bgcolor="#e3e3e3" class="style1"><div class="respuestasNormal" align="left">21/11/2013 07:00 PM</div></td><td bgcolor="#e3e3e3" class="respuestasNormal"><div align="left"> MEXICO D.F. Movimiento en centro de distribución</div></td><td bgcolor="#e3e3e3" class="style1"><div align="left" class="respuestasNormal">Envío pendiente de salida a ruta local </div></td></tr><tr><td bgcolor="#d6e3f5" class="style1"><div class="respuestasNormal" align="left">21/11/2013 06:56 PM</div></td><td bgcolor="#d6e3f5" class="respuestasNormal"><div align="left"> MEXICO D.F. Llegada a centro de distribución MEX MEXICO D.F.</div></td><td bgcolor="#d6e3f5" class="style1"><div align="left" class="respuestasNormal">&nbsp;</div></td></tr></tbody></table></div></div></td></tr></tbody></table><br><hr color="red" width="688px"><br><input type="hidden" name="tipoGuia" value="ESTAFETA&quot;"></form><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="1"> Versión 4.0 </font></div></td></tr></tbody></table>';
        $this->_hunter = new DomHunter();
        $this->_hunter->strHtmlObjetivo = $html;

        $presas = array();
        $presas[] = array('numero_guia', new KeyValue('mero de guía'));
        $presas[] = array('codigo_rastreo', new KeyValue('digo de rastreo'));
        $presas[] = array('origen', new KeyValue('origen'));
        $presas[] = array('destino', new KeyValue('destino', TRUE, TRUE));
        $presas[] = array('cp_destino', new IdUnico(5, 'num'));
        $presas[] = array('servicio', new KeyValue('entrega garantizada', FALSE));
        $presas[] = array('estatus', new NodoDom(array('find' => '.respuestasazul'), 'plaintext', 1));
        $presas[] = array('fecha_recoleccion', new KeyValue('fecha de recoleccion'));
        $presas[] = array('fecha_programada', new KeyValue('de entrega', TRUE, TRUE));
        $presas[] = array('fecha_entrega', new KeyValue('Fecha y hora de entrega'));
        $presas[] = array('tipo_envio', new KeyValue('tipo de envio'));
        $presas[] = array('peso', new KeyValue('Peso kg'));
        $presas[] = array('peso_volumetrico', new KeyValue('Peso volumétrico kg'));
        $presas[] = array('recibio', new KeyValue('Recibió'));
        $presas[] = array('dimensiones', new KeyValue('Dimensiones cm'));
        $columnas = array('fecha', 'lugar_movimiento', 'comentarios');
        $presas[] = array('movimientos', new Tabla(array('ocurrencia' => -1), $columnas, 3));
        $this->_hunter->arrPresas = $presas;
        $this->_hunted = $this->_hunter->hunt();
    }

    public function testNumeroGuia() {
        $this->assertEquals(self::$_assert['numero_guia'], $this->_hunted['numero_guia']);
    }

    public function testCodigoRastreo() {
        $this->assertEquals(self::$_assert['codigo_rastreo'], $this->_hunted['codigo_rastreo']);
    }

    public function testOrigen() {
        $this->assertEquals(self::$_assert['origen'], $this->_hunted['origen']);
    }

    public function testDestino() {
        $this->assertEquals(self::$_assert['destino'], $this->_hunted['destino']);
    }

    public function testCPDestino() {
        $this->assertEquals(self::$_assert['cp_destino'], $this->_hunted['cp_destino']);
    }

    public function testServicio() {
        $this->assertEquals(self::$_assert['servicio'], $this->_hunted['servicio']);
    }

    public function testEstatusEnvio() {
        $this->assertEquals(self::$_assert['estatus'], $this->_hunted['estatus']);
    }

    public function testFechaRecoleccion() {
        $this->assertEquals(self::$_assert['fecha_recoleccion'], $this->_hunted['fecha_recoleccion']);
    }

    public function testFechaProgramada() {
        $this->assertEquals(self::$_assert['fecha_programada'], $this->_hunted['fecha_programada']);
    }

    public function testFechaEntrega() {
        $this->assertEquals(self::$_assert['fecha_entrega'], $this->_hunted['fecha_entrega']);
    }

    public function testTipoEnvio() {
        $this->assertEquals(self::$_assert['tipo_envio'], $this->_hunted['tipo_envio']);
    }

    public function testRecibio() {
        $this->assertEquals(self::$_assert['recibio'], $this->_hunted['recibio']);
    }

    public function testFirmaRecibido() {
        $presas = array();
        $opciones_navegacion = array('getElementById' => $this->_hunted['numero_guia'] . 'FIR', 'nextSibling' => '', 'find' => 'img');
        $presas[] = array('firma_recibido', new NodoDom(array('navegacion' => $opciones_navegacion), 'src'));
        $this->_hunter->arrPresas = $presas;
        $hunted_firma = $this->_hunter->hunt();
        if (isset($hunted['firma_recibido'])) {
            $this->_hunted['firma_recibido'] = URL_FIRMA . $hunted_firma['firma_recibido'];
        } else {
            $this->_hunted['firma_recibido'] = '';
        }
        $this->assertEquals(self::$_assert['firma_recibido'], $this->_hunted['firma_recibido']);
    }

    public function testPeso() {
        $this->assertEquals(self::$_assert['peso'], $this->_hunted['peso']);
    }

    public function testPesoVolumetrico() {
        $this->assertEquals(self::$_assert['peso_volumetrico'], $this->_hunted['peso_volumetrico']);
    }

    public function testMovimientos() {
        $historial = $this->_hunted['movimientos'];
        $movimientos = array();
        foreach ($historial as $evento) {
            $movimiento = array();
            $movimiento['descripcion'] = $evento['lugar_movimiento'];
            $movimiento['fecha'] = $evento['fecha'];
            $estafeta = new Estafeta();
            $movimiento['id'] = $estafeta->id_movimiento($evento['lugar_movimiento']);
            $movimientos[] = $movimiento;
        }
        $this->_hunted['movimientos'] = $movimientos;
        $this->assertEquals(self::$_assert['movimientos'], $this->_hunted['movimientos']);
    }

    public function testInfoNoDisponible() {
        $this->markTestIncomplete('testInfoNoDisponible() no esta definido');
    }

}
