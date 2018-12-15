<?php
$input = <<<EOT
initial state: ##.#..########..##..#..##.....##..###.####.###.##.###...###.##..#.##...#.#.#...###..###.###.#.#

####. => #
##.#. => .
.##.# => .
..##. => .
..... => .
.#.#. => #
.###. => .
.#.## => .
#.#.# => .
.#... => #
#..#. => #
....# => .
###.. => .
##..# => #
#..## => #
..#.. => .
##### => .
.#### => #
#.##. => #
#.### => #
...#. => .
###.# => .
#.#.. => #
##... => #
...## => #
.#..# => .
#.... => .
#...# => .
.##.. => #
..### => .
##.## => .
..#.# => #
EOT;

function add_pots_needed_before(&$pots) {
  $earliest_plant_index = array_search('#', array_column($pots, 'pot'));
  
  $earliest_plant = $pots[$earliest_plant_index]['key'];

  $earliest_pot = $pots[0]['key'];

  if ($earliest_plant_index < 3) {  
    for ($i = 1; $i <= 3 - $earliest_plant; $i++) {
      array_unshift($pots, [
        'pot' => '.',
        'key' => $earliest_pot - $i,
      ]);
    }
  }
}

function add_pots_needed_after(&$pots) {
  $earliest_plant_index = array_search('#', array_column(array_reverse($pots), 'pot'));

  $earliest_plant = $pots[$earliest_plant_index]['key'];

  $latest_pot_index = max(array_keys($pots));

  $latest_pot = $pots[$latest_pot_index]['key'];

  if ($earliest_plant_index < 3) {  
    for ($i = 1; $i <= 3 - $earliest_plant_index; $i++) {
      $pots[] = [
        'pot' => '.',
        'key' => $latest_pot + $i,
      ];
    }
  }
}

function add_needed_pots(&$pots) {
  add_pots_needed_before($pots);
  add_pots_needed_after($pots);
}

function process_generation($pots_arr, $combinations) {
  add_needed_pots($pots_arr);

  $new_pots = $pots_arr;

  $i = 2;

  $end = max(array_keys($pots_arr)) - 2;

  while ($i <= $end) {
    $these_5_arrays = array_slice($pots_arr, $i -2, 5);

    $this_key = implode(array_column($these_5_arrays, 'pot'));

    $new_pots[$i]['pot'] = $combinations[$this_key];

    $i++;
  }

  return $new_pots;
}

preg_match('/initial state: (?<state>[#\.]+)/', $input, $matches);

$state = str_split($matches['state']);

preg_match_all('/(?<key>[#\.]+) => (?<value>#|\.)/', $input, $matches, PREG_SET_ORDER);

$combinations = [];

foreach ($matches as $match) {
  $combinations[$match['key']] = $match['value'];
}

$pots_arr = [];

foreach ($state as $key => $pot) {
  $pots_arr[] = [
    'key' => $key,
    'pot' => $pot,
  ];
} 

for ($i = 0; $i < 20; $i++) {
  $pots_arr = process_generation($pots_arr, $combinations);
}

$total_value = 0;

foreach ($pots_arr as $pot) {
  if ($pot['pot'] === '#') {
    $total_value += $pot['key'];
  }
}

echo $total_value;
