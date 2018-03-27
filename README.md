# StreamPHP

SICP like infinite stream for PHP.
(This is toy library, not practical.)

## Requirements

`php >= 5.6`

## Demo

```php:demo/integer_stream.php

function integers_start_from($n)
{
    return Stream::cons($n, function() use ($n) {return integers_start_from($n + 1);});
}
// 自然数の無限ストリーム
$naturals = integers_start_from(1);
echo "natural numbers:\n";
display_line($naturals, 20);

// natural numbers:
// 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20
```

## License

Copyright (c) 2010 deltam (deltam@gmail.com).

Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php)