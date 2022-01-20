<?php

namespace App\Command\CheckDirectory\Output;

use App\Command\CheckDirectory\Output\Helper\Model\Table;
use LeoVie\PhpCleanCode\Model\Score;
use LeoVie\PhpCleanCode\Rule\FileRuleResults;
use LeoVie\PhpCleanCode\Rule\RuleResult\Compliance;
use LeoVie\PhpCleanCode\Rule\RuleResult\RuleResult;
use LeoVie\PhpCleanCode\Rule\RuleResult\Violation;
use LeoVie\PhpHtmlBuilder\Model\Attribute;
use LeoVie\PhpHtmlBuilder\Model\Content;
use LeoVie\PhpHtmlBuilder\Model\HtmlDOM;
use LeoVie\PhpHtmlBuilder\Model\Tag;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\StopwatchEvent;

class HtmlOutput implements Output
{
    public const FORMAT = 'html';

    private const BOOTSTRAP_CSS_URL = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css';
    private const BOOTSTRAP_CSS_INTEGRITY = 'sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC';
    private const BOOTSTRAP_JS_URL = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js';
    private const BOOTSTRAP_JS_INTEGRITY = 'sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM';
    private const HIGHLIGHT_CSS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/styles/default.min.css';
    private const HIGHLIGHT_JS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/highlight.min.js';

    private SymfonyStyle $symfonyStyle;

    public function getFormat(): string
    {
        return self::FORMAT;
    }

    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): self
    {
        $this->symfonyStyle = $symfonyStyle;

        return $this;
    }

    public function noViolations(): self
    {
        $htmlDOM = HtmlDOM::create();
        $htmlDOM->add(
            Tag::create('html',
                [],
                [
                    $this->createHead(),
                    $this->createEmptyBody(),
                ]
            )
        );

        return $this->writeReport($htmlDOM);
    }

    private function createHead(): Tag
    {
        return Tag::create('head',
            [],
            [
                Tag::create('meta',
                    [Attribute::create('charset', 'utf-8')],
                    []
                ),
                Tag::create('meta',
                    [
                        Attribute::create('name', 'viewport'),
                        Attribute::create('content', 'width=device-width, initial-scale=1'),
                    ],
                    []
                ),
                Tag::create('link',
                    [
                        Attribute::create('href', self::BOOTSTRAP_CSS_URL),
                        Attribute::create('integrity', self::BOOTSTRAP_CSS_INTEGRITY),
                        Attribute::create('rel', 'stylesheet'),
                        Attribute::create('crossorigin', 'anonymous'),
                    ],
                    []
                ),
                Tag::create('link',
                    [
                        Attribute::create('href', self::HIGHLIGHT_CSS_URL),
                        Attribute::create('rel', 'stylesheet'),
                    ],
                    []
                ),
                Tag::create('title',
                    [],
                    [Content::create('php-clean-code: Report')]
                ),
            ]
        );
    }

    private function createEmptyBody(): Tag
    {
        return Tag::create('body',
            [],
            [
                Tag::create('div',
                    [Attribute::create('class', 'container')],
                    [
                        Tag::create('h1',
                            [],
                            [Content::create('php-clean-code: Report')]
                        ),
                        Tag::create('p',
                            [],
                            [Content::create('No violations.')]
                        ),
                    ]
                ),
            ],
        );
    }

    private function writeReport(HtmlDOM $htmlDOM): self
    {
        \Safe\file_put_contents(__DIR__ . '/../../../../report.html', $htmlDOM->asCode());

        return $this;
    }

    /** @inheritDoc */
    public function scoresResults(array $scoresResults, bool $showOnlyViolations): self
    {
        $divs = [];
        foreach ($scoresResults as $scoresResult) {
            $fileRuleResults = $scoresResult->getFileRuleResults();

            if ($showOnlyViolations) {
                if (empty($fileRuleResults->getRuleResultCollection()->getViolations())) {
                    continue;
                }
            }

            $fileRuleResultsTable = $this->createFileRuleResultsTable($fileRuleResults, $showOnlyViolations);
            $scoresTable = $this->createScoresTable($scoresResult->getScores());

            $div = Tag::create('div',
                [],
                [
                    Tag::create('h4',
                        [],
                        [Content::create($fileRuleResults->getPath())]
                    ),
                    Tag::create('h5',
                        [],
                        [Content::create('Rule results')]
                    ),
                    Tag::create('table',
                        [Attribute::create('class', 'table')],
                        [
                            $this->createTableHead($fileRuleResultsTable),
                            $this->createTableBody($fileRuleResultsTable),
                        ]
                    ),
                    Tag::create('h5',
                        [],
                        [Content::create('Scores')]
                    ),
                    Tag::create('table',
                        [Attribute::create('class', 'table')],
                        [
                            $this->createTableHead($scoresTable),
                            $this->createTableBody($scoresTable),
                        ]
                    ),
                ]
            );

            $divs[] = $div;
        }

        $htmlDOM = HtmlDOM::create();
        $htmlDOM->add(
            Tag::create('html',
                [],
                [
                    $this->createHead(),
                    Tag::create('body',
                        [],
                        [
                            Tag::create('script',
                                [
                                    Attribute::create('src', self::BOOTSTRAP_JS_URL),
                                    Attribute::create('integrity', self::BOOTSTRAP_JS_INTEGRITY),
                                    Attribute::create('crossorigin', 'anonymous'),
                                ],
                                []
                            ),
                            Tag::create('script',
                                [
                                    Attribute::create('src', self::HIGHLIGHT_JS_URL),
                                ],
                                []
                            ),
                            Tag::create('script',
                                [],
                                [Content::create('hljs.highlightAll();')]
                            ),
                            Tag::create('div',
                                [Attribute::create('class', 'container')],
                                [
                                    Tag::create('h1',
                                        [],
                                        [Content::create('php-clean-code: Report')]
                                    ),
                                    ...$divs,
                                ]
                            ),
                        ]
                    ),
                ]
            )
        );

        $this->symfonyStyle->newLine();

        return $this->writeReport($htmlDOM);
    }

    private function createFileRuleResultsTable(FileRuleResults $fileRuleResults, bool $showOnlyViolations): Table
    {
        $ruleResults = $this->extractRuleResultsFromFileRuleResults($fileRuleResults, $showOnlyViolations);

        $table = Table::create(['#', 'State', 'Rule', 'Message', 'Criticality']);
        foreach ($ruleResults as $ruleResult) {
            $table->addRow([
                $this->getStateByRuleResult($ruleResult),
                $ruleResult->getRule()->getName(),
                $ruleResult->getMessage(),
                $ruleResult->getCriticality() === null ? '' : $ruleResult->getCriticality() . ' %',
            ]);
        }

        return $table;
    }

    /** @return RuleResult[] */
    private function extractRuleResultsFromFileRuleResults(
        FileRuleResults $fileRuleResults,
        bool            $onlyViolations
    ): array
    {
        if ($onlyViolations) {
            return $fileRuleResults->getRuleResultCollection()->getViolations();
        }

        return $fileRuleResults->getRuleResultCollection()->getRuleResults();
    }

    /** @param Score[] $scores */
    private function createScoresTable(array $scores): Table
    {
        $table = Table::create(['#', 'Score type', 'Points']);
        foreach ($scores as $score) {
            $table->addRow([
                $score->getScoreType(),
                (string)$score->getPoints(),
            ]);
        }

        return $table;
    }

    private function getStateByRuleResult(RuleResult $ruleResult): string
    {
        return match (true) {
            $ruleResult instanceof Compliance => Tag::create('p', [Attribute::create('class', 'text-success')], [Content::create('Compliance')])->asCode(),
            $ruleResult instanceof Violation => Tag::create('p', [Attribute::create('class', 'text-danger')], [Content::create('Violation')])->asCode(),
            default => Tag::create('p', [Attribute::create('class', 'text-warning')], [Content::create('Warning')])->asCode(),
        };
    }

    public function stopTime(StopwatchEvent $stopwatchEvent): self
    {
        $this->symfonyStyle->writeln($stopwatchEvent->__toString());

        return $this;
    }

    public function createProgressIterator(iterable $iterable): iterable
    {
        return $this->symfonyStyle->createProgressBar()->iterate($iterable);
    }

    private function createTableHead(Table $table): Tag
    {
        return Tag::create('thead',
            [],
            [
                Tag::create('tr',
                    [],
                    array_map(
                        fn(string $columnCaption): Tag => $this->createColumnTh($columnCaption),
                        $table->getHeader()
                    )
                ),
            ]
        );
    }

    private function createColumnTh(string $caption): Tag
    {
        return Tag::create('th',
            [Attribute::create('scope', 'col')],
            [Content::create($caption)]
        );
    }

    private function createTableBody(Table $table): Tag
    {
        $tableRows = [];
        foreach ($table->getRows() as $i => $row) {
            $tableRows[] = $this->createTableRow($i + 1, $row);
        }

        return Tag::create('tbody',
            [],
            $tableRows
        );
    }

    /** @param array<int, string> $row */
    private function createTableRow(int $index, array $row): Tag
    {
        return Tag::create('tr',
            [],
            [
                $this->createRowTh((string)$index),
                ...array_map(fn(string $cellCaption): Tag => $this->createTd($cellCaption), $row),
            ]
        );
    }

    private function createRowTh(string $caption): Tag
    {
        return Tag::create('th',
            [Attribute::create('scope', 'row')],
            [Content::create($caption)]
        );
    }

    private function createTd(string $caption): Tag
    {
        return Tag::create('td',
            [],
            [Content::create($caption)]
        );
    }
}