<?php

namespace App\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class PostsTable extends DataTableComponent
{
    protected $model = Post::class;
    protected $index = 0;

    protected $status;

    protected $can_view = false;
    protected $can_edit = false;
    protected $can_delete = false;

    public function configure($status = null): void
    {
        $this->status = $status;
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setTableAttributes([
            'class' => 'border table-bordered display text-nowrap',
        ]);

        $this->can_view = true; // $this->checkPermission('merchant.branch.view');
        $this->can_edit = in_array(auth()->user()->group_id, [1, 2]) ? true : false; // $this->checkPermission('merchant.branch.edit');
        $this->can_delete = true; // $this->checkPermission('merchant.branch.delete');
    }

    public function builder(): Builder
    {
        $query = Post::query()->with(['category', 'status'])->select(
            'posts.id',
            'posts.title',
            'posts.category_id',
            'posts.status_id',
            'posts.seo_status',
            'posts.published_at',
            'posts.total_views'
        );

        $scopes = [
            'published' => fn($q) => $q->isPublished(),
            'scheduled' => fn($q) => $q->isScheduled(),
            'draft'     => fn($q) => $q->where('posts.status_id', 1),
            'featured'  => fn($q) => $q->isFeatured(),
            'breaking'  => fn($q) => $q->isBreaking(),
        ];

        if (isset($scopes[$this->status])) {
            $scopes[$this->status]($query);
        }

        return $query;
    }

    public function columns(): array
    {
        $columns = [
            Column::make("#", "id")
                ->sortable()
                ->format(fn() => ++$this->index),
            Column::make("Title", "title")
                ->sortable()
                ->searchable(),
            Column::make("Category", "category.name")
                ->sortable()
                ->searchable(),
            Column::make("Status", "status.name")
                ->sortable()
                ->searchable(),
            Column::make("SEO", "seo_status")
                ->sortable()
                ->label(
                    function ($row, Column $column) {
                        info($row->seo_status);
                        if ($row->seo_status >= 80) return '<i class="fas fa-circle text-success"></i>';
                        else if ($row->seo_status >= 60) return '<i class="fas fa-circle text-warning"></i>';
                        else return '<i class="fas fa-circle text-danger"></i>';
                    }
                )->html(),
            Column::make("Published On", "published_at")
                ->sortable()
                ->searchable()
                ->format(fn($value) => $value ? date('d M Y', strtotime($value)) : '-'),
            Column::make("Views", "total_views")
                ->sortable()
                ->format(fn($value) => $value ?? '0'),
        ];

        $columns[] = Column::make('Action', 'status_id')
            ->label(
                function ($row, Column $column) {
                    $html = '<div class="action-btn">';
                    if ($this->can_view) $html .= '<a href="' . route('preview', $row->id) . '" target="_blank" class="btn-action"><i class="fas fa-eye text-default"></i></a>';
                    if ($row->status_id != 4) {
                        if ($this->can_edit || (!$this->can_edit && $row->status_id != 3)) {
                            $html .= '<a href="' . route('posts.edit', $row->id) . '" class="btn-edit btn-action"><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                            $html .= '<a href="#" class="btn-trash btn-action" data-href="' . route('posts.destroy', $row->id) . '" data-id="' . $row->id . '" data-name="' . $row->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                        }
                    } else {
                        $html .= '<a href="#" class="btn-recover btn-action" data-href="' . route('post.recover', $row->id) . '" data-id="' . $row->id . '" data-name="' . $row->title . '" data-bs-toggle="modal" data-bs-target="#recoverConfirmationModal"><i class="fas fa-sync-alt text-success"></i></a>';
                        if (auth()->user()->group_id == 1) $html .= '<a href="#" class="btn-delete-permanent btn-action" data-href="' . route('post.delete_permanently', $row->id) . '" data-id="' . $row->id . '" data-name="' . $row->title . '" data-bs-toggle="modal" data-bs-target="#permanentDeleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    }
                    return $html;
                }
            )
            ->html();



        return $columns;
    }
}
