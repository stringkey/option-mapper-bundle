<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Enum;

enum GroupKind: string
{
    case UserDefined = 'User defined';
    case System = 'System';

    /**
     * To make the groupKind selectable in a form return the options as an array
     * @return array<string,string>
     */
    public static function asArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, self $type) => $choices + [$type->name => $type->value],
            [],
        );
    }
}
