import uuid
from contextvars import ContextVar

# Context variable for request tracing
request_id_ctx: ContextVar[str] = ContextVar("request_id", default="")

class Tracer:
    @staticmethod
    def initialize_trace():
        trace_id = str(uuid.uuid4())
        request_id_ctx.set(trace_id)
        return trace_id

    @staticmethod
    def get_trace_id() -> str:
        return request_id_ctx.get()
