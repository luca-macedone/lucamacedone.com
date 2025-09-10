<?php

namespace App\Http\Controllers;

use App\Models\ProjectTechnology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Writer;

class TechnologyController extends Controller
{
    /**
     * Lista tecnologie
     */
    public function index(Request $request)
    {
        $query = ProjectTechnology::withCount('projects');

        // Filtro per categoria
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Ricerca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $technologies = $query->orderBy('name')->paginate(20);

        // Categorie disponibili
        $categories = ProjectTechnology::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort()
            ->values();

        $stats = [
            'total' => ProjectTechnology::count(),
            'with_projects' => ProjectTechnology::has('projects')->count(),
            'categories' => ProjectTechnology::distinct()->whereNotNull('category')->count('category'),
        ];

        return view('admin.technologies.index', compact('technologies', 'categories', 'stats'));
    }

    /**
     * Form creazione tecnologia
     */
    public function create()
    {
        $categories = ProjectTechnology::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort()
            ->values();

        return view('admin.technologies.create', compact('categories'));
    }

    /**
     * Form modifica tecnologia
     */
    public function edit(ProjectTechnology $technology)
    {
        $categories = ProjectTechnology::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort()
            ->values();

        $technology->load('projects:id,title,status');

        return view('admin.technologies.edit', compact('technology', 'categories'));
    }

    /**
     * Elimina tecnologia
     */
    public function destroy(ProjectTechnology $technology)
    {
        try {
            // Verifica se la tecnologia ha progetti associati
            if ($technology->projects()->count() > 0) {
                return redirect()->route('admin.technologies.index')
                    ->with('error', 'Impossibile eliminare la tecnologia: è utilizzata in ' . $technology->projects()->count() . ' progetti');
            }

            $technology->delete();

            return redirect()->route('admin.technologies.index')
                ->with('success', 'Tecnologia eliminata con successo');
        } catch (\Exception $e) {
            return redirect()->route('admin.technologies.index')
                ->with('error', 'Errore nell\'eliminare la tecnologia: ' . $e->getMessage());
        }
    }

    /**
     * Export tecnologie in CSV
     */
    public function export()
    {
        try {
            $technologies = ProjectTechnology::withCount('projects')->get();

            $csv = Writer::createFromString('');
            $csv->insertOne(['ID', 'Nome', 'Slug', 'Categoria', 'Colore', 'Icona', 'Progetti', 'Creata', 'Modificata']);

            foreach ($technologies as $tech) {
                $csv->insertOne([
                    $tech->id,
                    $tech->name,
                    $tech->slug,
                    $tech->category ?? '',
                    $tech->color,
                    $tech->icon ?? '',
                    $tech->projects_count,
                    $tech->created_at->format('Y-m-d H:i:s'),
                    $tech->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            $filename = 'tecnologie_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csv->toString(), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.technologies.index')
                ->with('error', 'Errore nell\'export: ' . $e->getMessage());
        }
    }

    /**
     * Import tecnologie da CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $csv = Reader::createFromPath($request->file('csv_file')->path(), 'r');
            $csv->setHeaderOffset(0);

            $imported = 0;
            $errors = [];

            foreach ($csv as $offset => $record) {
                try {
                    // Valida dati richiesti
                    if (empty($record['Nome'])) {
                        $errors[] = "Riga {$offset}: Nome mancante";
                        continue;
                    }

                    $technology = ProjectTechnology::updateOrCreate(
                        ['slug' => Str::slug($record['Nome'])],
                        [
                            'name' => $record['Nome'],
                            'category' => $record['Categoria'] ?? null,
                            'color' => $record['Colore'] ?? '#6B7280',
                            'icon' => $record['Icona'] ?? null,
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Riga {$offset}: " . $e->getMessage();
                }
            }

            $message = "Import completato: {$imported} tecnologie importate";
            if (!empty($errors)) {
                $message .= ". Errori: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " e altri " . (count($errors) - 3) . " errori";
                }
            }

            return redirect()->route('admin.technologies.index')
                ->with($imported > 0 ? 'success' : 'warning', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.technologies.index')
                ->with('error', 'Errore nell\'import: ' . $e->getMessage());
        }
    }

    /**
     * Search tecnologie per autocomplete (AJAX)
     */
    public function searchTechnologies(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $technologies = ProjectTechnology::where('name', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->withCount('projects')
            ->limit(15)
            ->get(['id', 'name', 'category', 'color', 'icon'])
            ->map(function ($tech) {
                return [
                    'id' => $tech->id,
                    'text' => $tech->name,
                    'category' => $tech->category,
                    'color' => $tech->color,
                    'icon' => $tech->icon,
                    'projects_count' => $tech->projects_count
                ];
            });

        return response()->json($technologies);
    }
}
