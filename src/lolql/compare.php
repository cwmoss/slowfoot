<?php

namespace lolql;

/*
title == "hello"
title == subtitle
authors._ref == "a19"
*/
//  {"t":"k","c":["playground"],"v":["center-flexbox.html","center-grid.html"]} ~ {"t":"k","c":["defined"],"v":null}
//  {"t":"k","c":["playground"],"v":null} ~ {"t":"k","c":["defined"],"v":null}
function cmp_eq($l, $r) {
    dbg('cmp_eq +++ ', $l, $r);
    if ($l['t'] == 'k' && $r['t'] == 'k' && $r["c"] == ["defined"]) {
        dbg("++ defined-test");
        return $l['v'] ? true : false;
    }
    if ($l['t'] == 'k' && is_array($l['v'])) {
        return array_search($r['v'][0], $l['v']);
    }
    if ($l['t'] == 'k') {
        return ($l['v'] == $r['v'][0]);
    }
    if ($r['t'] == 'k' && is_array($r['v'])) {
        return array_search($l['v'][0], $r['v']);
    }
    if ($r['t'] == 'k') {
        return ($l['v'][0] == $r['v']);
    }
    return ($l['v'][0] == $r['v'][0]);
}

function cmp_lt($l, $r) {
    // dbg('cmp +++ < ', $l, $r);

    if ($l['t'] == 'k' && is_array($l['v'])) {
        return false;
    }
    if ($l['t'] == 'k') {
        return ($l['v'] < $r['v'][0]);
    }
    if ($r['t'] == 'k' && is_array($r['v'])) {
        return false;
    }
    if ($r['t'] == 'k') {
        return ($l['v'][0] < $r['v']);
    }
    return ($l['v'][0] < $r['v'][0]);
}

function cmp_gt($l, $r) {
    // dbg('cmp +++ > ', $l, $r);

    if ($l['t'] == 'k' && is_array($l['v'])) {
        return false;
    }
    if ($l['t'] == 'k') {
        return ($l['v'] > $r['v'][0]);
    }
    if ($r['t'] == 'k' && is_array($r['v'])) {
        return false;
    }
    if ($r['t'] == 'k') {
        return ($l['v'][0] > $r['v']);
    }
    return ($l['v'][0] > $r['v'][0]);
}

/*
title matches "world"
title matches "world*"
title matches "*world"
*/
function cmp_matches($l, $r) {
    if ($r['t'] != 'v') {
        return false;
    }
    $val = $r['c'][0];
    // arrays as name not supported for now
    if ($val[0] == '*') {
        return str_ends_with($l['v'], ltrim($val, '*'));
    }

    if ($val[-1] == '*') {
        return str_starts_with($l['v'], rtrim($val, '*'));
    }

    return str_contains($l['v'], $val);
}

/*
title in ["Aliens", "Interstellar", "Passengers"]
"yolo" in tags
*/
function cmp_in($l, $r) {
    if ($l['t'] == 'k') {
        $haystack = $l['v'];
        $needle = $r['v'][0];
    } else {
        $haystack = $r['v'];
        $needle = $r['v'][0];
    }
    return in_array($needle, $haystack);
}
