<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\Domain\DomainCreateRequest;
    use App\Http\Requests\Domain\DomainUpdateRequest;
    use App\Http\Resources\DomainResource;
    use App\Models\Domain;
    use Illuminate\Http\Request;

    class DomainController extends Controller
    {
        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            $domains = Domain::query()
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate(10)
                ->withQueryString();

            if ($request->expectsJson()) {
                return DomainResource::collection($domains);
            }

            return view('pages.domains.index', compact('domains'));
        }

        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function create(Request $request)
        {
            return view('pages.domains.form');
        }

        /**
         * @param DomainCreateRequest $request
         * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
         */
        public function store(DomainCreateRequest $request)
        {
            $validated = $request->validated();

            $domain = Domain::create([
                'domain' => $validated['domain'],
                'user_id' => $request->user()->id,
            ]);

            if ($request->expectsJson()) {
                return (new DomainResource($domain))->response()->setStatusCode(201);
            }

            return redirect()
                ->route('domains.index')
                ->with('success', 'Domain created successfully.');
        }

        /**
         * @param Request $request
         * @param Domain $domain
         * @return DomainResource|\Illuminate\Http\RedirectResponse
         */
        public function show(Request $request, Domain $domain)
        {
            abort_unless($domain->user_id === $request->user()->id, 403);

            if ($request->expectsJson()) {
                return new DomainResource($domain);
            }

            return redirect()->route('domains.index');
        }

        /**
         * @param Request $request
         * @param Domain $domain
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function edit(Request $request, Domain $domain)
        {
            abort_unless($domain->user_id === $request->user()->id, 403);

            return view('pages.domains.form', compact('domain'));
        }

        /**
         * @param DomainUpdateRequest $request
         * @param Domain $domain
         * @return DomainResource|\Illuminate\Http\RedirectResponse
         */
        public function update(DomainUpdateRequest $request, Domain $domain)
        {
            abort_unless($domain->user_id === $request->user()->id, 403);

            $validated = $request->validated();

            $domain->update([
                'domain' => $validated['domain'],
            ]);

            if ($request->expectsJson()) {
                return new DomainResource($domain->fresh());
            }

            return redirect()
                ->route('domains.index')
                ->with('success', 'Domain updated successfully.');
        }

        /**
         * @param Request $request
         * @param Domain $domain
         * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
         */
        public function destroy(Request $request, Domain $domain)
        {
            abort_unless($domain->user_id === $request->user()->id, 403);

            $domain->delete();

            if ($request->expectsJson()) {
                return response()->noContent();
            }

            return redirect()
                ->route('domains.index')
                ->with('success', 'Domain deleted successfully.');
        }
    }