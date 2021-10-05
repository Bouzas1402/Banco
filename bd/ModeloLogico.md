## Base de datos:

```
R (id_cuenta, numero_cuenta, IBAN, contraseña, fecha_de_apertura, nombre, apellidos, DNI, telefono, nacionalidad, pais, comunidad, provincia, ciudad, calle, codigo_postal, saldo, fecha_mov, gastos, ingresos, tarjeta, tipo_tarjeta)
```

```
CUENTA (id, numero_cuenta, contraseña, IBAN, fecha_de_apertura, saldo, fecha_mov, ingresos, gastos, num_tarjeta, pin)

TITULAR (DNI, nombre, apellidos, , telefono, nacionalidad, pais, comunidad, provincia, ciudad, calle, codigo_postal)
```
CUENTA (id_cuenta(PK), numero_cuenta , IBAN , contraseña, saldo, fecha_de_apertura, DNI(FK))
TARJETA (id_cuenta(PK)(FK), num_tarjeta, pin)
MOVIMIENTOS_CUENTA (id_cuenta(PK)(FK), fecha_movimiento(PK) cantidad, I/S)
CLIENTE (DNI (PK),nombre, apellido1, apellido2, nacionalidad, telefono)
VIVIENDA_CLIENTE (DNI(PK)(FK), pais, comunidad, provincia, ciudad, calle, numero, cod_postal)
```