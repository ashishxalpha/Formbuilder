import time
from typing import Dict, Any

class MetricsLogger:
    @staticmethod
    def record(metric_name: str, value: Any, tags: Dict[str, str] = None):
        # In a real system, this would push to Prometheus/Datadog
        print(f"[METRIC] {metric_name}: {value} | Tags: {tags}")

    @staticmethod
    def increment(metric_name: str, tags: Dict[str, str] = None):
        MetricsLogger.record(metric_name, 1, tags)

class PerformanceTimer:
    def __init__(self, metric_name: str, tags: Dict[str, str] = None):
        self.metric_name = metric_name
        self.tags = tags
        self.start_time = None

    def __enter__(self):
        self.start_time = time.time()
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        duration_ms = round((time.time() - self.start_time) * 1000, 2)
        MetricsLogger.record(f"{self.metric_name}_latency_ms", duration_ms, self.tags)
