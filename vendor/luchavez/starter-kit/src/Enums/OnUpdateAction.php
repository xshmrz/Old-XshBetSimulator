<?php

namespace Luchavez\StarterKit\Enums;

enum OnUpdateAction: string
{
    case CASCADE = 'cascade';
    case RESTRICT = 'restrict';
}
