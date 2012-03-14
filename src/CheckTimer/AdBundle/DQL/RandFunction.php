<?php

namespace CheckTimer\AdBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

/**
 * RAND() function
 *
 * This function returns a random number.
 *
 * NOTE: It isn't fully implemented!
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class RandFunction extends FunctionNode
{
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'RAND()';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
