<?php

namespace slowfoot\dev;

use Psr\Http\Message\ServerRequestInterface as R;
use React\Http\Message\Response as P;

use slowfoot\project;

class api_index {

    public function __construct(public project $project) {
    }

    public function fetch(): P {
        $this->project->config->fresh_store();
        $this->project->load(true);
        return P::json(["ok" => true]);
    }

    public function info(): P {
        $info = $this->project->ds->info();
        return P::json($info);
    }

    public function id(R $r): P {
        $row = $this->project->ds->get($r->getQueryParams()["id"]);
        $links = $this->project->ds->path_get_all($r->getQueryParams()["id"]);
        return P::json(["doc" => $row, "links" => $links]);
    }

    public function type(R $r): P {
        $type = $r->getAttribute("type");
        dbg("[api] type", $type);

        $page = $r->getAttribute("page", 1);

        if ($type == '__paths') {
            if ($this->project->ds->db->db)
                $rows = $this->project->ds->db->db->safeQuery('SELECT * FROM paths LIMIT ? OFFSET ?', [20, 0]);
            else $rows = array_values(array_map(fn($p) => [
                "id" => $p["_"],
                "path" => $p["_"],
                "name" => "_"
            ], $this->project->ds->db->paths));
        } else {
            $rows = $this->project->ds->query_type($type);
        }
        return P::json(['rows' => $rows]);
    }

    public function lolql(R $r): P {
        $query = $r->getQueryParams()["query"] ?? "";
        $query = trim($query);
        //$row = $db->row('SELECT _id, _type, body FROM docs WHERE _id = ? ', $id);
        $rows = $this->project->ds->query($query);
        return P::json($rows);
    }

    public function fts(R $r): P {
        $q = $r->getQueryParams()["q"] ?? "";
        $rows = $this->project->ds->query_sql("SELECT _id, snippet(docs_fts,1, '<b>', '</b>', '[...]', 30) body FROM docs_fts WHERE docs_fts = ? ", [$q]);
        return P::json($rows);
    }

    public function __invoke(R $r): P {
        $path = $r->getUri()->getPath();
        $parts = explode("/", $path);
        return match ($parts[2]) {
            "info", "index" => $this->info(),
            "id" => $this->id($r),
            "type" => $this->type($r),
            "fts" => $this->fts($r),
            "lolql" => $this->lolql($r),
            "fetch" => $this->fetch()
        };
    }
}
