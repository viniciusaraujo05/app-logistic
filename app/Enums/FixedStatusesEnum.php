<?php

namespace App\Enums;

final class FixedStatusesEnum
{
    public const string TO_APPROVE = 'Por Aprovar';

    public const string TO_PAY = 'Por pagar';

    public const string PREPARATION = 'Preparação';

    public const string TO_SEND = 'Por Enviar';

    public const string SENT = 'Enviado';

    public const string DELIVERED = 'Entregue';
}
