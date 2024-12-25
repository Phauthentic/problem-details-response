<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails;

/**
 * ErrorResponseInterface
 *
 * @link https://www.rfc-editor.org/rfc/rfc9457.html
 */
interface ErrorResponseInterface
{
    /**
     * Problem Details "type" member is a JSON string with a URI reference [URI]
     * identifying the problem type. Consumers MUST use the "type" URI (after
     * resolution, if necessary) as the primary identifier for the problem type.
     *
     * When not present, the value is assumed to be "about:blank".
     *
     * If the type URI is a locator (e.g., with an "http" or "https" scheme),
     * dereferencing it SHOULD provide human-readable documentation for the
     * problem type (e.g., using HTML [HTML5]). However, consumers SHOULD NOT
     * automatically dereference the type URI unless providing information to
     * developers (e.g., when using a debugging tool).
     *
     * When "type" contains a relative URI, it is resolved relative to the
     * document's base URI (as per [URI], Section 5). Using relative URIs may
     * cause confusion and might not be handled correctly by all implementations.
     * It is RECOMMENDED to use absolute URIs in "type" when possible. If relative
     * URIs are used, include the full path (e.g., "/types/123").
     *
     * The type URI is allowed to be a non-resolvable URI. For example, the tag
     * URI scheme [TAG] can be used to uniquely identify problem types:
     *
     * tag:example@example.org,2021-09-17:OutOfLuck
     *
     * Resolvable type URIs are encouraged for future resolution capabilities,
     * as using non-resolvable URIs may introduce breaking changes when adopting
     * tools that resolve type URIs to discover information about the error.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9457.html#name-type
     * @return string
     */
    public function getType(): string;

    /**
     * Problem Details "status" member is a JSON number indicating the
     * HTTP status code ([HTTP], Section 15) from the origin server for
     * this occurrence of the problem.
     *
     * If present, the "status" member is advisory, conveying the HTTP status
     * code for consumer convenience. Generators MUST use the same status
     * code in the actual HTTP response to ensure compatibility with generic
     * HTTP software. See Section 5 for caveats regarding its use.
     *
     * Consumers can use the "status" member to identify the original status
     * code when changed (e.g., by an intermediary or cache) or when a
     * message's content is persisted without HTTP information. Generic
     * HTTP software will still utilize the HTTP status code.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9457.html#name-status
     * @return int
     */
    public function getStatus(): int;

    /**
     * Problem Details "title" member is a JSON string with a short,
     * human-readable summary of the problem type.
     *
     * It SHOULD NOT change across occurrences, except for localization
     * (e.g., proactive content negotiation; see [HTTP], Section 12.1).
     *
     * The "title" string is advisory and included for users unaware of or
     * unable to discover the semantics of the type URI (e.g., during offline log analysis).
     *
     * @link https://www.rfc-editor.org/rfc/rfc9457.html#name-title
     * @return null|string
     */
    public function getTitle(): ?string;

    /**
     * Problem Details "detail" member contains a JSON string with a
     * human-readable explanation specific to this occurrence of the problem.
     *
     * If present, the "detail" string should focus on helping the client
     * correct the problem, avoiding debugging information.
     *
     * Consumers SHOULD NOT parse the "detail" member for information;
     * extensions are recommended for obtaining such details in a more
     * suitable and less error-prone manner.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9457.html#name-detail
     * @return null|string
     */
    public function getDetail(): ?string;

    /**
     * Problem Details "instance" member provides a JSON string with a URI
     * referencing the specific occurrence of the problem.
     *
     * When dereferenceable, it allows fetching the problem details object.
     * Proactive content negotiation (see [HTTP], Section 12.5.1) may provide
     * additional information in various formats.
     *
     * If the "instance" URI is not dereferenceable, it serves as a unique
     * identifier opaque to the client but potentially significant to the server.
     *
     * When "instance" contains a relative URI, resolve it relative to the
     * document's base URI (as per [URI], Section 5). However, relative URIs
     * may cause confusion and might not be handled correctly by all
     * implementations. It is RECOMMENDED to use absolute URIs in "instance"
     * when possible. If relative URIs are used, include the full path (e.g., "/instances/123").
     *
     * @link https://www.rfc-editor.org/rfc/rfc9457.html#name-instance
     * @return null|string
     */
    public function getInstance(): ?string;

    /**
     * Extension Members
     *
     * Problem type definitions may extend the problem details object with additional
     * members that are specific to that problem type.
     *
     * For example, our out-of-credit problem above defines two such extensions --
     * "balance" and "accounts" to convey additional, problem-specific information.
     *
     * Similarly, the "validation error" example defines an "errors" extension that
     * contains a list of individual error occurrences found, with details and a
     * pointer to the location of each.
     *
     * Clients consuming problem details must ignore any such extensions that they
     * don't recognize; this allows problem types to evolve and include additional
     * information in the future.
     *
     * When creating extensions, problem type authors should choose their names
     * carefully. To be used in the XML format (see Appendix B), they will need to
     * conform to the Name rule in Section 2.3 of [XML].
     *
     * @return array<string, mixed>
     */
    public function getExtensions(): array;

    /**
     * Checks if the error response is of the same type.
     *
     * @param ErrorResponseInterface $errorResponse
     * @return bool
     */
    public function isSameType(ErrorResponseInterface $errorResponse): bool;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
