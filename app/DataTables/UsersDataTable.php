<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function($row){
                $user = auth()->user();
                $btn = '<a href="'.route('users.edit',$row->uuid).'" class="edit btn btn-blue btn-sm">Editar perfil</a>';
                if(!$row->hasRole('administrator','institution','coordinator')){
                    $btn .= '<a href="'.route('assessments.index',$row->account_id).'" class="btn btn-warning btn-sm">Evaluaciones</a>';
                }
                return $btn;
            })
            ->addColumn('role', function ($user) {
                $role = $user->roles->pluck('name')->implode(', ');
                if($role == 'respondent'){
                    $rol = 'Evaluado';
                } elseif($role == 'institution'){
                    $rol = 'Instituto';
                } else {
                    $rol = 'Administrador';
                }

                return $rol;
            })
            ->addColumn('created', function($row) {
                return $row->created_at->format('d-m-Y');
            })
            ->rawColumns(['action','role'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        if (auth()->user()->hasRole('administrator')) {
            return $model->newQuery();
        } elseif(auth()->user()->hasRole('institution')){
            return $model->newQuery()->where('user_id',auth()->user()->id)->role(['respondent']);
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('users-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    //->selectStyleSingle()
                    ->buttons([
                        //Button::make('excel'),
                        //Button::make('csv'),
                        //Button::make('pdf'),
                        Button::make('print'),
                        //Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Nombre')->width(160),
            Column::make('role')->title('Rol'),
            //Column::make('created')->title('Fecha')->width(90),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(250)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
