<?php

namespace Fluent\Orm\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', [\Fluent\Orm\Pagination\PaginationState::class, 'resolveUsing']);
