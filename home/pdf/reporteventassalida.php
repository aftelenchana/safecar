<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <style media="screen">
  html{
  margin-left: 22px;
  margin-right: 22px;
  margin-top: 28px;
  margin-bottom: 28px;
}
*,::before,::after{
  margin: 0px;
  padding: 0px;
  box-sizing: border-box;
}
body{
  font-size: 12px;
  font-weight: 400;
  color: #212529;
}
body, html {
  font-family: sans-serif;
}
table {
  width: 100%;
}
/* table {
  display: table;
  border-collapse: collapse;
  border-color: grey;
} */

.th {
  font-size: 14px;
  color: #fff;
  line-height: 1.4;
  background-color: #6c7ae0; /*#6c7ae0 */
  padding-top: 10px;
  padding-bottom: 10px;
}
.head{
  /* padding-top: 12px;
  padding-bottom: 12px; */
}
.center{
  text-align: center;
}
p{
  margin-top: 0;
  margin-bottom: 0;
}
ul{
  list-style-type: none;
}
.tablepe > tr:nth-child(even) {
  background-color: #f8f6ff;
}
.tablepe{
  /* border: 1px solid black;*/
   border-collapse: collapse;
}
.body > th{
/*  border: 1px solid rgb(49, 49, 49);*/
  border: 1px solid rgb(29, 29, 29); /*#6c7ae0*/
}
.body > td{
  border: 1px solid rgb(29, 29, 29);
}
  </style>
  <body>

    <div class="container">
        <table style="padding-bottom: 12px; padding-top: 10px;">
            <thead>
                <tr>
                    <th align="left">{{nombre_comercial}}</th>
                    <th align="center">{{titulo_reporte}}</th>
                    <th align="right" >{{fechas}}</th>
                </tr>
            </thead>
        </table>

        <table  class="tablepe">
            <thead>
                <tr class="body">
                    <th class="center th" width="5%">#</th>
                    <th class="center th" width="8%">Fecha</th>
                    <th class="center th"  width="6%">Serie</th>
                    <th class="center th"  width="6%">Numero</th>
                    <th class="center th"  width="12%">Cliente</th>
                    <!-- <th class="center th">Productos Vendidos</th> -->
                    <th class="center th">
                        <table>
                        <tbody>
                            <tr >
                                <td class="center" colspan="5"> Productos Vendidos </td>
                            </tr>
                            <tr >
                                <td  class="center"  width="16%" >Codigo</td>
                                <td class="center" width="39%"  >Nombre Producto</td>
                                <td class="center" width="12%" >Cantidad</td>
                                <td class="center" width="16%"  >Precio</td >
                                <td class="center"   >Sub.Total</td >>
                            </tr>
                        </tbody>
                        </table>
                    </td>
                    <th class="center th" width="8%">Total</th>
                </tr>
            </thead>
            <tbody>
              <tr >
                  <td class="center" width="16%">{{codigo}}</td>
                  <td width="39%" >{{nombre_producto}}</td>
                  <td class="center" width="12%" >{{cantidad}}</td>
                  <td class="center" width="16%" >{{precio}}</td>
                  <td class="center" >{{sub_total}}</td>
              </tr>

            </tbody>
        </table>

    </div>

  </body>
</html>
