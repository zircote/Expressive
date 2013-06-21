# Expressive

[![Build Status](https://secure.travis-ci.org/zircote/Expressive.png)](http://travis-ci.org/zircote/Expressive)

A PHP expression parser. This tool was largely inspired by snips of code I came across
on the web while looking for some form of an expression DSL. The approach inspired me
enough to feel it deserved sharing. Unfortunately these snippets had no name associated
with them and I am therefor unable to attribute the inspirative work to its inceptor.


## Use:

### Inline PHP
```php
<?php
$expression = new \Expressive\Parser('(2+2)*sqrt(4)');
echo $expression;
// 8
```

### CLI:
_this is intended more as an example use_

```
> bin/expr

math > (2+2)*sqrt(4)
8
math > exit
>

```

Supported Operations:
 - `+` addition `2+2`
 - `-` substraction `4-2`
 - `/` division `4/2`
 - `*` multiplication `2*2`
 - `^` exponential `2^2`
 - `sin` sine `sin(60)`
 - `cos` cosine `cos(90)`
 - `tan` tangent `tan(45)`
 - `sqrt` square root `sqrt(4)`
 - `exp` exponent `exp(12)`
