<?php

// Home
Breadcrumbs::register('inicio', function($breadcrumbs)
{
    $breadcrumbs->push('Inicio', url('/'));
//    $breadcrumbs->push($title, $url);
});

// Inicio > Permisos
Breadcrumbs::register('permisos', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Permisos', route('admin.permissions.index'));
});

// Inicio > Roles
Breadcrumbs::register('roles', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Roles', route('admin.roles.index'));
});

// Inicio > Apertura
Breadcrumbs::register('apertura', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('ESIGEF', route('admin.apertura.index'));
});

// Inicio > Cierre
Breadcrumbs::register('cierre', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Cierre Mensual', route('admin.historico.cierre'));
});

// Inicio > Direcciones
Breadcrumbs::register('area', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Direcciones', route('admin.areas.index'));
});

// Inicio >  Direcciones >Coordinaciones
Breadcrumbs::register('departamento', function($breadcrumbs)
{
    $breadcrumbs->parent('area');
    $breadcrumbs->push('Coordinaciones', route('admin.departamentos.index'));
});

// Inicio >  Direcciones > Coordinaciones > Responsables
Breadcrumbs::register('trabajador', function($breadcrumbs)
{
    $breadcrumbs->parent('departamento');
    $breadcrumbs->push('Responsables', route('admin.workers.index'));
});

// Inicio  > Programas
Breadcrumbs::register('programa', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Programas', route('admin.programas.index'));
});

// Inicio Programas > Actividades

Breadcrumbs::register('actividad', function($breadcrumbs)
{
    $breadcrumbs->parent('programa');
    $breadcrumbs->push('Actividades', route('admin.actividades.index'));
});

// Inicio > POA-FDG
Breadcrumbs::register('poa-fdg', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('POA-FDG', route('admin.items.index'));
});

// Inicio > Programas > Actividades > Items

Breadcrumbs::register('item', function($breadcrumbs)
{
    $breadcrumbs->parent('actividad');
    $breadcrumbs->push('Items', route('admin.items.index'));
});

// Inicio > POA-FDG > Presupuesto
Breadcrumbs::register('plan-area', function($breadcrumbs)
{
    $breadcrumbs->parent('poa-fdg');
    $breadcrumbs->push('Presupuesto', route('admin.poa.index'));
});

// Inicio > POA-FDG > Extras
Breadcrumbs::register('extras', function($breadcrumbs)
{
    $breadcrumbs->parent('poa-fdg');
    $breadcrumbs->push('Ingresos-Extras', route('admin.ingresos.index'));
});


// Inicio > PAC
Breadcrumbs::register('pac-plan', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('PAC', route('indexPlanificacion'));
});

// Inicio > PAC > Procesos
Breadcrumbs::register('pac-proceso', function($breadcrumbs)
{
    $breadcrumbs->parent('pac-plan');
    $breadcrumbs->push('Procesos', route('admin.pacs.index'));
});

// Inicio > PAC > Procesos > gestion
Breadcrumbs::register('pac-gestion', function($breadcrumbs)
{
    $breadcrumbs->parent('pac-proceso');
    $breadcrumbs->push('Gestión', route('admin.pacs.index'));
});

// Inicio > Reformas
Breadcrumbs::register('reformas', function($breadcrumbs)
{
    $breadcrumbs->push('Reformas', route('admin.reformas.index'));
});

// Inicio > Reformas
Breadcrumbs::register('reformas-solicitud', function($breadcrumbs)
{
    $breadcrumbs->parent('pac-proceso');
    $breadcrumbs->push('Solicitud-Reforma', route('admin.reformas.index'));
});

// Inicio > Reportes
Breadcrumbs::register('reporte-mensual', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Resumen mensual', route('admin.reportes.resumen_mensual'));
});

// Inicio > Historico
Breadcrumbs::register('historico', function($breadcrumbs)
{
    $breadcrumbs->parent('inicio');
    $breadcrumbs->push('Histórico', route('admin.historico.index'));
});








// Home > Blog > [Category]
Breadcrumbs::register('category', function($breadcrumbs, $category)
{
    $breadcrumbs->parent('blog');
    $breadcrumbs->push($category->title, route('category', $category->id));
});

// Home > Blog > [Category] > [Page]
Breadcrumbs::register('page', function($breadcrumbs, $page)
{
    $breadcrumbs->parent('category', $page->category);
    $breadcrumbs->push($page->title, route('page', $page->id));
});
