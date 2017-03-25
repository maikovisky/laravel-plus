
    /**
     * Show the form for creating the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ${{camel_name}}= $this->repository->model();

        return view('{{snake_name}}.create', compact('{{camel_name}}'));
    }

