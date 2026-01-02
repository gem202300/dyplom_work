<?php

namespace App\Enums\Auth;

use App\Enums\Traits\EnumToArray;

enum PermissionType: string
{
    use EnumToArray;

    case USER_ACCESS = 'user_access';
    case USER_MANAGE = 'user_manage';

    case ATTRACTION_ACCESS = 'attraction_access';
    case ATTRACTION_MANAGE = 'attraction_manage';

    case CATEGORY_ACCESS = 'category_access';
    case CATEGORY_MANAGE = 'category_manage';

    case NOCLEG_VIEW = 'nocleg_view';

    case NOCLEG_MANAGE = 'nocleg_manage';

    case NOCLEG_OWNER_MANAGE = 'nocleg_owner_manage';

    case RATING_VIEW = 'rating_view';
    case RATING_CREATE = 'rating_create';
    case RATING_MANAGE = 'rating_manage';

    case BANNED_WORDS_MANAGE = 'banned_words_manage';

    case MY_NOCLEGI_ACCESS = 'my_noclegi_access';
}