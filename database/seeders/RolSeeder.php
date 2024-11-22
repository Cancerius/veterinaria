<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //roles
        $role1 = Role::create(['name'=>'Administrador']);
        $role2 = Role::create(['name'=>'Veterinario']);

        //permisos
        Permission::create(['name'=>'Pagina principal'])->syncRoles([$role1,$role2]);
        //veterinario
        Permission::create(['name'=>'Tabla veterinario'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Crear veterinario'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Editar veterinario'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Eliminar veterinario'])->syncRoles([$role1,]);

        //roles
        Permission::create(['name'=>'Tabla roles'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Crear roles'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Editar roles'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Eliminar roles'])->syncRoles([$role1,]);
        //propietarios
        Permission::create(['name'=>'Tabla propietario'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear propietario'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Editar propietario'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Eliminar propietario'])->syncRoles([$role1,$role2]);
        //mascotas
        Permission::create(['name'=>'Tabla mascota'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear mascota'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Editar mascota'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Eliminar mascota'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Generar Kardex mascota'])->syncRoles([$role1,$role2]);
        //consultas
        Permission::create(['name'=>'Tabla consulta'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear consulta'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Editar consulta'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Eliminar consulta'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte consulta'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte por fechas consulta'])->syncRoles([$role1,$role2]);
        //cirugias
        Permission::create(['name'=>'Tabla cirugia'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear cirugia'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Editar cirugia'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Eliminar cirugia'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte cirugia'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte por fechas cirugia'])->syncRoles([$role1,$role2]);
        //vacunas
        Permission::create(['name'=>'Tabla vacunacion'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear vacunacion'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Editar vacunacion'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Eliminar vacunacion'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte vacunacion'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte por fechas vacuna'])->syncRoles([$role1,$role2]);
        //peluqueria
        Permission::create(['name'=>'Tabla peluqueria y baño'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear peluqueria y baño'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Editar peluqueira y baño'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Eliminar peluqueriay baño'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte peluqueria y baño'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Reporte por fechas peluqueria y baño'])->syncRoles([$role1,$role2]);
        //productos
        Permission::create(['name'=>'Tabla productos'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'Crear producto'])->syncRoles([$role1]);
        Permission::create(['name'=>'Editar producto'])->syncRoles([$role1,]);
        Permission::create(['name'=>'Eliminar producto'])->syncRoles([$role1,]);

        
        
    }
}
