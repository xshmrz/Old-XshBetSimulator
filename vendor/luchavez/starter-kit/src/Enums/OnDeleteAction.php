<?php

namespace Luchavez\StarterKit\Enums;

enum OnDeleteAction: string
{
    case CASCADE = 'cascade';
    case RESTRICT = 'restrict';
    case SET_NULL = 'set null';
    case NO_ACTION = 'no action';
}
