<?php

namespace App\Http\Controllers\APIRetail;

use App\Models\Consultas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;


class ConsultasController extends Controller
{
    //Muestra las cifras de las tiendas por una sola base de datos
     public function ventasTiendas(Request $request){

        if(empty($request->only('dateDay'))){

            return response()->json([
                'status' => "404",
                'message' => 'Se requiere ingresar una fecha para la consulta'
            ]);

        }else{

            $date = $request['dateDay'];

             $tiendas = DB::connection('mysqlRetail')->select('
             select "' . $date . '" as Fecha, a.Sucursal, if(b.Venta_Total is null,0,b.Venta_Total) as Venta_Total, if(b.N_Folios is null,0,b.N_Folios) as N_Folios, if(b.Piezas is null,0,b.Piezas) as Piezas,
if(b.PzaxTicket is null,0,b.PzaxTicket) as PzaxTicket, if(b.TicketPromedio is null,0,b.TicketPromedio) as TicketProm
from (select CATALM,CATDESCR,concat(CATDESCR," ",CATALM) as Sucursal from db199fusionretail.falmcat
where CATZONA<>99 and CATALM<>"" and length(CATALM)=3 order by CATALM) a
#Query: Ventas xAlmacen RFG
left join (select DFECHA as Fecha, CATDESCR as Sucursal, AIALMACEN as Suc, (sum(AICANTF * AIPRECIO) * 1.16) as Venta_Total,count(distinct (DNUM)) as N_Folios, sum(AICANTF) as Piezas,
round(sum(AICANTF) / count(distinct(DNUM)), 1) as PzaxTicket,round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),1) as TicketPromedio
from db199fusionretail.fdoc
        inner join db199fusionretail.faxinv on fdoc.DSEQ = faxinv.DSEQ
        inner join db199fusionretail.finv on faxinv.ISEQ = finv.ISEQ
        inner join db199fusionretail.falmcat on faxinv.AIALMACEN = falmcat.CATALM
where AITIPMV in ("T") and DFECHA = "' . $date . '" group by AIALMACEN , DFECHA order by AIALMACEN, DFECHA) b on a.CATALM=b.Suc
union
#Query: Almacenes DHFashon
select "' . $date . '" as Fecha, a.Sucursal, if(b.Venta_Total is null,0,b.Venta_Total) as Venta_Total, if(b.N_Folios is null,0,b.N_Folios) as N_Folios, if(b.Piezas is null,0,b.Piezas) as Piezas,
if(b.PzaxTicket is null,0,b.PzaxTicket) as PzaxTicket, if(b.TicketPromedio is null,0,b.TicketPromedio) as TicketProm
from (select CATALM,CATDESCR,concat(CATDESCR," ",CATALM) as Sucursal from db199dhfashion.falmcat where CATZONA<>99 and CATALM<>"" and length(CATALM)=3 order by CATALM) a
#Query: Ventas xAlmacen DHFashion
left join (select DFECHA as Fecha, CATDESCR as Sucursal, AIALMACEN as Suc, (sum(AICANTF * AIPRECIO) * 1.16) as Venta_Total,count(distinct (DNUM)) as N_Folios, sum(AICANTF) as Piezas,
round(sum(AICANTF) / count(distinct(DNUM)), 1) as PzaxTicket,round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),1) as TicketPromedio
from db199dhfashion.fdoc
        inner join db199dhfashion.faxinv on fdoc.DSEQ = faxinv.DSEQ
        inner join db199dhfashion.finv on faxinv.ISEQ = finv.ISEQ
        inner join db199dhfashion.falmcat on faxinv.AIALMACEN = falmcat.CATALM
where AITIPMV in ("T") and DFECHA = "' . $date . '" group by AIALMACEN , DFECHA order by AIALMACEN, DFECHA) b on a.CATALM=b.Suc
union
#Query: Almacenes Hirsor
select "' . $date . '" as Fecha, a.Sucursal, if(b.Venta_Total is null,0,b.Venta_Total) as Venta_Total, if(b.N_Folios is null,0,b.N_Folios) as N_Folios, if(b.Piezas is null,0,b.Piezas) as Piezas,
if(b.PzaxTicket is null,0,b.PzaxTicket) as PzaxTicket, if(b.TicketPromedio is null,0,b.TicketPromedio) as TicketProm
#Query: Ventas xAlmacen Hirsor
from (select CATALM,CATDESCR,concat(CATDESCR," ",CATALM) as Sucursal from db199hirsor.falmcat where CATZONA<>99 and CATALM<>"" and length(CATALM)=3 order by CATALM) a
left join (select DFECHA as Fecha, CATDESCR as Sucursal, AIALMACEN as Suc, (sum(AICANTF * AIPRECIO) * 1.16) as Venta_Total,count(distinct (DNUM)) as N_Folios, sum(AICANTF) as Piezas,
round(sum(AICANTF) / count(distinct(DNUM)), 1) as PzaxTicket,round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),1) as TicketPromedio
from db199hirsor.fdoc
        inner join db199hirsor.faxinv on fdoc.DSEQ = faxinv.DSEQ
        inner join db199hirsor.finv on faxinv.ISEQ = finv.ISEQ
        inner join db199hirsor.falmcat on faxinv.AIALMACEN = falmcat.CATALM
where AITIPMV in ("T") and DFECHA = "' . $date . '" group by AIALMACEN , DFECHA order by AIALMACEN, DFECHA) b on a.CATALM=b.Suc;

             ');


            /*
            //Ventas por día
 $tiendas = DB::connection('mysqlRetail')->select('

            select "' . $date . '" as Fecha, round((sum(AICANTF * AIPRECIO) * 1.16),2) as Venta_Total,count(distinct (DNUM)) as tickets, sum(AICANTF) as piezas,
round(sum(AICANTF) / count(distinct(DNUM)), 2) as PzaxTicket,round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),2) as TicketPromedio,
round((((round((sum(AICANTF * AIPRECIO) * 1.16),2)) - (round((sum(AICANTF * AICOSTO) * 1.16),2)))*100)/(round((sum(AICANTF * AIPRECIO) * 1.16),2)),2) as utilidad from db199fusionretail.fdoc	inner join db199fusionretail.faxinv on fdoc.DSEQ = faxinv.DSEQ	inner join db199fusionretail.finv on faxinv.ISEQ = finv.ISEQ where AITIPMV in ("T") and DFECHA = "' . $date . '" group by DFECHA union
#Query: Ventas xDia DHFashion
select "' . $date . '" as Fecha, round((sum(AICANTF * AIPRECIO) * 1.16),2) as Venta_Total,count(distinct (DNUM)) as tickets, sum(AICANTF) as piezas,
round(sum(AICANTF) / count(distinct(DNUM)), 2) as PzaxTicket,round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),2) as TicketPromedio,
round((((round((sum(AICANTF * AIPRECIO) * 1.16),2)) - (round((sum(AICANTF * AICOSTO) * 1.16),2)))*100)/(round((sum(AICANTF * AIPRECIO) * 1.16),2)),2) as utilidad
from db199dhfashion.fdoc	inner join db199dhfashion.faxinv on fdoc.DSEQ = faxinv.DSEQ	inner join db199dhfashion.finv on faxinv.ISEQ = finv.ISEQ
where AITIPMV in ("T") and DFECHA = "' . $date . '" group by DFECHA
union
#Query: Ventas xDia Hirsor
select "' . $date . '" as Fecha, round((sum(AICANTF * AIPRECIO) * 1.16),2) as Venta_Total,count(distinct (DNUM)) as tickets, sum(AICANTF) as piezas,
round(sum(AICANTF) / count(distinct(DNUM)), 2) as PzaxTicket,round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),2) as TicketPromedio,
round((((round((sum(AICANTF * AIPRECIO) * 1.16),2)) - (round((sum(AICANTF * AICOSTO) * 1.16),2)))*100)/(round((sum(AICANTF * AIPRECIO) * 1.16),2)),2) as utilidad
from db199hirsor.fdoc	inner join db199hirsor.faxinv on fdoc.DSEQ = faxinv.DSEQ	inner join db199hirsor.finv on faxinv.ISEQ = finv.ISEQ
where AITIPMV in ("T") and DFECHA = "' . $date . '" group by DFECHA;');

            //Primer query
            $tiendas = DB::connection('mysqlRetail')->select('
        SELECT DFECHA AS Fecha,
        CATDESCR AS Sucursal, AIALMACEN AS Suc,
        (SUM(AICANTF * AIPRECIO) * 1.16) AS Venta_Total,
        COUNT(DISTINCT (DNUM)) AS N_Folios,
        SUM(AICANTF) AS Piezas,
        ROUND(SUM(AICANTF) / COUNT(DISTINCT (DNUM)), 1) AS PzaxTicket,
        ROUND(SUM(AICANTF * (AIPRECIO * 1.16)) / COUNT(DISTINCT (DNUM)),1) AS TicketPromedio
        FROM fdoc INNER JOIN faxinv ON fdoc.DSEQ = faxinv.DSEQ
        INNER JOIN finv ON faxinv.ISEQ = finv.ISEQ
        INNER JOIN falmcat ON faxinv.AIALMACEN = falmcat.CATALM
        WHERE AITIPMV IN ("T") AND DFECHA = "' . $date . '"
        GROUP BY AIALMACEN,
        DFECHA ORDER BY AIALMACEN,
        DFECHA');

        */

        return response()->json($tiendas);

        }

    }

    //public function


    public function index(Request $request){

        if(empty($request->only('dateStart', 'dateEnd'))){

            return response()->json([
                'status' => "404",
                'message' => 'Se requieren ingresar datos para la consulta'
            ]);

        }else{

            $Ini = $request['dateStart'];
            $Fin = $request['dateEnd'];

            $consultas = DB::connection('mysqlRetail')->select('
        select "' .$Ini. '" as Fecha,
        round((sum(AICANTF * AIPRECIO) * 1.16),2) as Venta_Total,
        count(distinct (DNUM)) as tickets, sum(AICANTF) as piezas,
        round(sum(AICANTF) / count(distinct(DNUM)), 2) as PzaxTicket,
        round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),2) as TicketPromedio,
        round((((round((sum(AICANTF * AIPRECIO) * 1.16),2)) - (round((sum(AICANTF * AICOSTO) * 1.16),2)))*100)/(round((sum(AICANTF * AIPRECIO) * 1.16),2)),2) as utilidad
        from db199fusionretail.fdoc
        inner join db199fusionretail.faxinv on fdoc.DSEQ = faxinv.DSEQ
        inner join db199fusionretail.finv on faxinv.ISEQ = finv.ISEQ
        where AITIPMV in ("T") and DFECHA = "' .$Ini. '"
        group by DFECHA
        union
        select  "'.$Fin.'" as Fecha,
        round((sum(AICANTF * AIPRECIO) * 1.16),2) as Venta_Total,
        count(distinct (DNUM)) as tickets, sum(AICANTF) as piezas,
        round(sum(AICANTF) / count(distinct(DNUM)), 2) as PzaxTicket,
        round(sum(AICANTF * (AIPRECIO * 1.16)) / count(distinct (DNUM)),2) as TicketPromedio,
        round((((round((sum(AICANTF * AIPRECIO) * 1.16),2)) - (round((sum(AICANTF * AICOSTO) * 1.16),2)))*100)/(round((sum(AICANTF * AIPRECIO) * 1.16),2)),2) as utilidad
        from db199fusionretail.fdoc	inner join db199fusionretail.faxinv on fdoc.DSEQ = faxinv.DSEQ	inner join db199fusionretail.finv on faxinv.ISEQ = finv.ISEQ
        where AITIPMV in ("T") and DFECHA = "'.$Fin.'" group by DFECHA;');

        return response()->json([
            'present' => $consultas[0],
            'past' => $consultas[1]]
        );

        }

        /*
                if(empty($fecha)){

            $consultaFecha = DB::connection('mysqlRetail')->select('SELECT DFECHA AS Fecha, CATDESCR AS Sucursal, AIALMACEN AS Suc, (SUM(AICANTF * AIPRECIO) * 1.16) AS Venta_Total, COUNT(DISTINCT (DNUM)) AS N_Folios, SUM(AICANTF) AS Piezas, ROUND(SUM(AICANTF) / COUNT(DISTINCT (DNUM)), 1) AS PzaxTicket, ROUND(SUM(AICANTF * (AIPRECIO * 1.16)) / COUNT(DISTINCT (DNUM)),1) AS TicketPromedio FROM fdoc INNER JOIN faxinv ON fdoc.DSEQ = faxinv.DSEQ INNER JOIN finv ON faxinv.ISEQ = finv.ISEQ INNER JOIN falmcat ON faxinv.AIALMACEN = falmcat.CATALM WHERE AITIPMV IN ("T") AND DFECHA = 20221105 GROUP BY AIALMACEN , DFECHA ORDER BY AIALMACEN , DFECHA');

        }else{

            $consultaFecha = DB::connection('mysqlRetail')->select('SELECT DFECHA AS Fecha, CATDESCR AS Sucursal, AIALMACEN AS Suc, (SUM(AICANTF * AIPRECIO) * 1.16) AS Venta_Total, COUNT(DISTINCT (DNUM)) AS N_Folios, SUM(AICANTF) AS Piezas, ROUND(SUM(AICANTF) / COUNT(DISTINCT (DNUM)), 1) AS PzaxTicket, ROUND(SUM(AICANTF * (AIPRECIO * 1.16)) / COUNT(DISTINCT (DNUM)),1) AS TicketPromedio FROM fdoc INNER JOIN faxinv ON fdoc.DSEQ = faxinv.DSEQ INNER JOIN finv ON faxinv.ISEQ = finv.ISEQ INNER JOIN falmcat ON faxinv.AIALMACEN = falmcat.CATALM WHERE AITIPMV IN ("T") AND DFECHA = 20221105 GROUP BY AIALMACEN , DFECHA ORDER BY AIALMACEN , DFECHA');

        }
        */

    }
}
